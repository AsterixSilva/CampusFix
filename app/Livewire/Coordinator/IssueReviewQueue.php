<?php

declare(strict_types=1);

namespace App\Livewire\Coordinator;

use App\Enums\IssueStatus;
use App\Models\Issue;
use App\Models\User;
use App\Services\IssueWorkflow;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

final class IssueReviewQueue extends Component
{
    use WithPagination;

    public array $rejectionReasons = [];

    public function verify(int $issueId, IssueWorkflow $workflow): void
    {
        $coordinator = $this->coordinator();
        $issue = Issue::query()->findOrFail($issueId);

        $workflow->verify($issue, $coordinator);

        session()->flash('workflow-status', 'Issue verified.');
        $this->resetPage();
    }

    public function reject(int $issueId, IssueWorkflow $workflow): void
    {
        $coordinator = $this->coordinator();

        $this->validate([
            "rejectionReasons.{$issueId}" => ['required', 'string', 'max:2000'],
        ]);

        $issue = Issue::query()->findOrFail($issueId);
        $workflow->reject($issue, $coordinator, $this->rejectionReasons[$issueId]);

        unset($this->rejectionReasons[$issueId]);
        session()->flash('workflow-status', 'Issue rejected.');
        $this->resetPage();
    }

    public function render(): View
    {
        $this->coordinator();

        return view('livewire.coordinator.issue-review-queue', [
            'issues' => Issue::query()
                ->where('status', IssueStatus::Reported->value)
                ->orderBy('created_at')
                ->paginate(20),
        ]);
    }

    private function coordinator(): User
    {
        $actor = auth()->user();

        abort_unless(
            $actor instanceof User
            && in_array((string) $actor->role, ['coordinator', 'admin', 'super_admin'], true),
            403,
        );

        return $actor;
    }
}
