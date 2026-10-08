<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\IssueStatus;
use App\Events\IssueStatusChanged;
use App\Exceptions\InvalidIssueTransition;
use App\Models\Issue;
use App\Models\StatusHistory;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use LogicException;
use UnexpectedValueException;

final class IssueWorkflow
{
    /**
     * Record the reported status after the reporting service creates an issue.
     */
    public function recordInitialReported(
        Issue $issue,
        User $actor,
        ?string $reason = null,
    ): StatusHistory {
        return DB::transaction(function () use ($issue, $actor, $reason): StatusHistory {
            $lockedIssue = Issue::query()
                ->whereKey($issue->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($this->statusOf($lockedIssue) !== IssueStatus::Reported) {
                throw new DomainException('Only a reported issue can receive its initial status history.');
            }

            if (StatusHistory::query()->where('issue_id', $lockedIssue->getKey())->exists()) {
                throw new LogicException('Initial status history has already been recorded for this issue.');
            }

            $history = StatusHistory::query()->create([
                'issue_id' => $lockedIssue->getKey(),
                'from_status' => null,
                'to_status' => IssueStatus::Reported->value,
                'actor_id' => $actor->getKey(),
                'reason' => $this->normalizeReason($reason),
            ]);

            $this->dispatchAfterCommit($history);

            return $history;
        });
    }

    public function transition(
        Issue $issue,
        IssueStatus $target,
        User $actor,
        ?string $reason = null,
    ): StatusHistory {
        return DB::transaction(function () use ($issue, $target, $actor, $reason): StatusHistory {
            $lockedIssue = Issue::query()
                ->whereKey($issue->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $from = $this->statusOf($lockedIssue);

            if (! $from->canTransitionTo($target)) {
                throw new InvalidIssueTransition($from, $target);
            }

            Gate::forUser($actor)->authorize('transitionStatus', [$lockedIssue, $target]);

            $lockedIssue->forceFill(['status' => $target->value])->saveOrFail();

            $history = StatusHistory::query()->create([
                'issue_id' => $lockedIssue->getKey(),
                'from_status' => $from->value,
                'to_status' => $target->value,
                'actor_id' => $actor->getKey(),
                'reason' => $this->normalizeReason($reason),
            ]);

            $this->dispatchAfterCommit($history);

            return $history;
        });
    }

    private function statusOf(Issue $issue): IssueStatus
    {
        $rawStatus = $issue->getRawOriginal('status') ?? $issue->getAttribute('status');

        if ($rawStatus instanceof IssueStatus) {
            return $rawStatus;
        }

        return IssueStatus::tryFrom((string) $rawStatus)
            ?? throw new UnexpectedValueException(sprintf(
                'Issue %s has an unknown status "%s".',
                $issue->getKey(),
                (string) $rawStatus,
            ));
    }

    private function normalizeReason(?string $reason): ?string
    {
        $reason = $reason === null ? null : trim($reason);

        return $reason === '' ? null : $reason;
    }

    private function dispatchAfterCommit(StatusHistory $history): void
    {
        DB::afterCommit(static function () use ($history): void {
            event(new IssueStatusChanged($history));
        });
    }
}
