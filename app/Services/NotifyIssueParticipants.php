<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\IssueStatusChanged;
use App\Models\User;
use App\Models\GuestIssueReporter;
use App\Notifications\GuestIssueStatusUpdate;
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
            // Guest reporters receive mail updates below, without a user account.
        } else {
            Notification::send(
                User::query()->whereIn('id', $recipientIds)->get(),
                new IssueStatusNotification($history),
            );
        }

        GuestIssueReporter::query()
            ->where('issue_id', $issue->getKey())
            ->whereNotNull('email_verified_at')
            ->pluck('email')
            ->unique()
            ->each(static fn (string $email) => Notification::route('mail', $email)
                ->notify(new GuestIssueStatusUpdate($history)));
    }
}
