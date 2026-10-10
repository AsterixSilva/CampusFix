<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\StatusHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

final class IssueStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public StatusHistory $history)
    {
    }

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toDatabase(object $notifiable): array
    {
        $issue = $this->history->issue;

        return [
            'event' => 'issue_status_changed',
            'issue_id' => $this->history->issue_id,
            'issue_title' => $issue?->title,
            'from_status' => $this->history->from_status,
            'to_status' => $this->history->to_status,
            'actor' => $this->history->actor?->name,
            'reason' => $this->history->reason,
            'url' => $issue ? route('issues.show', $issue) : null,
        ];
    }
}
