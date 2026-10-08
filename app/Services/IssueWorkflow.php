<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\IssueStatus;
use App\Events\IssueStatusChanged;
use App\Exceptions\InvalidIssueTransition;
use App\Models\Assignment;
use App\Models\Issue;
use App\Models\Resolution;
use App\Models\StatusHistory;
use App\Models\Team;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use InvalidArgumentException;
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

            Gate::forUser($actor)->authorize('recordInitialReported', $lockedIssue);

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

    public function verify(
        Issue $issue,
        User $coordinator,
        ?string $reason = null,
    ): StatusHistory {
        return $this->transition($issue, IssueStatus::Verified, $coordinator, $reason);
    }

    public function reject(
        Issue $issue,
        User $coordinator,
        string $reason,
    ): StatusHistory {
        return $this->transition($issue, IssueStatus::Rejected, $coordinator, $reason);
    }

    public function assign(
        Issue $issue,
        Team $team,
        User $technician,
        User $coordinator,
    ): Assignment {
        return DB::transaction(function () use ($issue, $team, $technician, $coordinator): Assignment {
            $lockedIssue = Issue::query()
                ->whereKey($issue->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $status = $this->statusOf($lockedIssue);

            if (! in_array($status, [IssueStatus::Verified, IssueStatus::Assigned], true)) {
                throw new InvalidIssueTransition($status, IssueStatus::Assigned);
            }

            Gate::forUser($coordinator)->authorize(
                'assignIssue',
                [$lockedIssue, $team, $technician],
            );

            $assignedAt = now();

            Assignment::query()
                ->where('issue_id', $lockedIssue->getKey())
                ->whereNull('released_at')
                ->lockForUpdate()
                ->get()
                ->each(static function (Assignment $activeAssignment) use ($assignedAt): void {
                    $activeAssignment->forceFill(['released_at' => $assignedAt])->saveOrFail();
                });

            $assignment = Assignment::query()->create([
                'issue_id' => $lockedIssue->getKey(),
                'team_id' => $team->getKey(),
                'technician_id' => $technician->getKey(),
                'assigned_by' => $coordinator->getKey(),
                'assigned_at' => $assignedAt,
            ]);

            if ($status === IssueStatus::Verified) {
                $this->transition($lockedIssue, IssueStatus::Assigned, $coordinator);
            }

            return $assignment;
        });
    }

    public function acceptAssignment(Assignment $assignment, User $technician): Assignment
    {
        return DB::transaction(function () use ($assignment, $technician): Assignment {
            $assignmentSnapshot = Assignment::query()
                ->whereKey($assignment->getKey())
                ->firstOrFail();

            $lockedIssue = Issue::query()
                ->whereKey($assignmentSnapshot->issue_id)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedAssignment = Assignment::query()
                ->whereKey($assignmentSnapshot->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedAssignment->released_at !== null) {
                throw new DomainException('A released assignment cannot be accepted.');
            }

            if ($lockedAssignment->accepted_at !== null) {
                throw new DomainException('This assignment has already been accepted.');
            }

            Gate::forUser($technician)->authorize(
                'acceptAssignment',
                [$lockedIssue, $lockedAssignment],
            );

            $lockedAssignment->forceFill(['accepted_at' => now()])->saveOrFail();

            return $lockedAssignment;
        });
    }

    public function start(Issue $issue, User $technician): StatusHistory
    {
        return $this->transition($issue, IssueStatus::InProgress, $technician);
    }

    public function putOnHold(Issue $issue, User $technician, string $reason): StatusHistory
    {
        return $this->transition($issue, IssueStatus::OnHold, $technician, $reason);
    }

    public function updateProgress(Issue $issue, User $technician, string $body): int
    {
        $body = trim($body);

        if ($body === '') {
            throw new InvalidArgumentException('A progress update cannot be empty.');
        }

        return DB::transaction(function () use ($issue, $technician, $body): int {
            $lockedIssue = Issue::query()
                ->whereKey($issue->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            Gate::forUser($technician)->authorize('addProgressUpdate', $lockedIssue);

            $now = now();

            return (int) DB::table('comments')->insertGetId([
                'issue_id' => $lockedIssue->getKey(),
                'user_id' => $technician->getKey(),
                'body' => $body,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        });
    }

    public function resolve(
        Issue $issue,
        User $technician,
        string $rootCause,
        string $actionTaken,
        ?string $partsUsed,
        int $minutesSpent,
    ): Resolution {
        $rootCause = trim($rootCause);
        $actionTaken = trim($actionTaken);
        $partsUsed = $this->normalizeReason($partsUsed);

        if ($rootCause === '' || $actionTaken === '') {
            throw new InvalidArgumentException('Root cause and action taken are required to resolve an issue.');
        }

        if ($minutesSpent < 0) {
            throw new InvalidArgumentException('Minutes spent cannot be negative.');
        }

        return DB::transaction(function () use (
            $issue,
            $technician,
            $rootCause,
            $actionTaken,
            $partsUsed,
            $minutesSpent,
        ): Resolution {
            $lockedIssue = Issue::query()
                ->whereKey($issue->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $from = $this->statusOf($lockedIssue);

            if (! $from->canTransitionTo(IssueStatus::Resolved)) {
                throw new InvalidIssueTransition($from, IssueStatus::Resolved);
            }

            Gate::forUser($technician)->authorize(
                'transitionStatus',
                [$lockedIssue, IssueStatus::Resolved],
            );

            $assignment = Assignment::query()
                ->where('issue_id', $lockedIssue->getKey())
                ->where('technician_id', $technician->getKey())
                ->whereNotNull('accepted_at')
                ->whereNull('released_at')
                ->lockForUpdate()
                ->firstOrFail();

            $resolution = Resolution::query()->create([
                'issue_id' => $lockedIssue->getKey(),
                'assignment_id' => $assignment->getKey(),
                'resolved_by' => $technician->getKey(),
                'root_cause' => $rootCause,
                'action_taken' => $actionTaken,
                'parts_used' => $partsUsed,
                'minutes_spent' => $minutesSpent,
            ]);

            $this->transition($lockedIssue, IssueStatus::Resolved, $technician);

            $assignment->forceFill(['released_at' => now()])->saveOrFail();

            return $resolution;
        });
    }

    public function close(Issue $issue, User $reporter): StatusHistory
    {
        return $this->transition($issue, IssueStatus::Closed, $reporter);
    }

    public function reopen(Issue $issue, User $reporter, string $reason): StatusHistory
    {
        return $this->transition($issue, IssueStatus::Reopened, $reporter, $reason);
    }

    public function transition(
        Issue $issue,
        IssueStatus $target,
        User $actor,
        ?string $reason = null,
    ): StatusHistory {
        $reason = $this->normalizeReason($reason);
        $this->assertRequiredReason($target, $reason);

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
                'reason' => $reason,
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

    private function assertRequiredReason(IssueStatus $target, ?string $reason): void
    {
        if (
            in_array($target, [IssueStatus::Rejected, IssueStatus::OnHold, IssueStatus::Reopened], true)
            && $reason === null
        ) {
            throw new DomainException(sprintf(
                'A reason is required when moving an issue to "%s".',
                $target->value,
            ));
        }
    }

    private function dispatchAfterCommit(StatusHistory $history): void
    {
        DB::afterCommit(static function () use ($history): void {
            event(new IssueStatusChanged($history));
        });
    }
}
