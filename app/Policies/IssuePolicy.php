<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\IssueStatus;
use App\Models\Issue;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class IssuePolicy
{
    public function recordInitialReported(User $actor, Issue $issue): bool
    {
        $rawStatus = $issue->getRawOriginal('status') ?? $issue->getAttribute('status');
        $status = $rawStatus instanceof IssueStatus
            ? $rawStatus
            : IssueStatus::tryFrom((string) $rawStatus);

        return (string) $actor->role === 'member'
            && $status === IssueStatus::Reported
            && $this->isReporter($actor, $issue);
    }

    public function transitionStatus(User $actor, Issue $issue, IssueStatus $target): bool
    {
        $rawStatus = $issue->getRawOriginal('status') ?? $issue->getAttribute('status');
        $from = $rawStatus instanceof IssueStatus
            ? $rawStatus
            : IssueStatus::tryFrom((string) $rawStatus);

        if ($from === null || ! $from->canTransitionTo($target)) {
            return false;
        }

        return match ($from->value.'->'.$target->value) {
            'reported->verified',
            'reported->rejected',
            'verified->assigned',
            'verified->rejected',
            'reopened->verified' => $this->isCoordinator($actor),

            'assigned->in_progress',
            'in_progress->on_hold',
            'in_progress->resolved',
            'on_hold->in_progress' => $this->isAcceptedTechnician($actor, $issue),

            'resolved->closed',
            'resolved->reopened',
            'closed->reopened' => $this->isReporter($actor, $issue),

            default => false,
        };
    }

    private function isCoordinator(User $actor): bool
    {
        return in_array((string) $actor->role, ['coordinator', 'admin', 'super_admin'], true);
    }

    private function isAcceptedTechnician(User $actor, Issue $issue): bool
    {
        if ((string) $actor->role !== 'technician') {
            return false;
        }

        return DB::table('assignments')
            ->where('issue_id', $issue->getKey())
            ->where('technician_id', $actor->getKey())
            ->whereNotNull('accepted_at')
            ->whereNull('released_at')
            ->exists();
    }

    private function isReporter(User $actor, Issue $issue): bool
    {
        // The Issue model owns this relation because issue_user is shared with reporting.
        if (! method_exists($issue, 'reporters')) {
            return false;
        }

        return $issue->reporters()
            ->whereKey($actor->getKey())
            ->exists();
    }
}
