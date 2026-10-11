<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\IssueStatusChanged;
use App\Models\User;
use App\Notifications\IssueStatusNotification;
use Illuminate\Support\Facades\Notification;

final class NotifyIssueParticipants
{
    public function handle(IssueStatusChanged $event): void
    {
        $history = $event->history->loadMissing([
            'issue.reporters',
            'issue.affectedUsers',
            'issue.followers',
            'actor',
        ]);
        $issue = $history->issue;

        if ($issue === null) {
            return;
        }

        $recipientIds = collect()
            ->merge($issue->reporters->modelKeys())
            ->merge($issue->affectedUsers->modelKeys())
            ->merge($issue->followers->modelKeys())
            ->merge($issue->assignments()->whereNull('released_at')->pluck('technician_id'))
            ->when(
                in_array($history->to_status, ['reported', 'reopened'], true),
                fn ($ids) => $ids->merge(User::query()
                    ->whereIn('role', ['coordinator', 'admin', 'super_admin'])
                    ->pluck('id')),
            )
            ->unique()
            ->reject(fn ($id): bool => (string) $id === (string) $history->actor_id)
            ->values();

        if ($recipientIds->isEmpty()) {
            return;
        }

        Notification::send(
            User::query()->whereIn('id', $recipientIds)->get(),
            new IssueStatusNotification($history),
        );
    }
}
