<?php

declare(strict_types=1);

namespace App\Enums;

enum IssueStatus: string
{
    case Reported = 'reported';
    case Verified = 'verified';
    case Assigned = 'assigned';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';
    case Closed = 'closed';
    case Rejected = 'rejected';
    case OnHold = 'on_hold';
    case Reopened = 'reopened';
    case Merged = 'merged';

    /**
     * Allowed edges in the MVP workflow.
     *
     * Merged is intentionally terminal and unreachable until duplicate merging
     * has a confirmed target-issue contract.
     *
     * @var array<string, list<string>>
     */
    private const TRANSITIONS = [
        'reported' => ['verified', 'rejected'],
        'verified' => ['assigned', 'rejected'],
        'assigned' => ['in_progress'],
        'in_progress' => ['resolved', 'on_hold'],
        'on_hold' => ['assigned', 'in_progress'],
        'resolved' => ['closed', 'reopened'],
        'closed' => ['reopened'],
        'reopened' => ['verified'],
        'rejected' => [],
        'merged' => [],
    ];

    public function canTransitionTo(self $next): bool
    {
        return in_array($next->value, self::TRANSITIONS[$this->value], true);
    }
}
