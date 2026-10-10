<?php

declare(strict_types=1);

namespace App\Livewire\Reporter;

use App\Enums\IssueStatus;
use App\Models\Issue;
use App\Models\User;
use App\Services\IssueWorkflow;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

final class ConfirmationQueue extends Component
{
    use WithPagination;

    public array $reopenReasons = [];

    public function closeIssue(int $issueId, IssueWorkflow $workflow): void
    {
        $reporter = $this->reporter();

        $issue = $this->reporterIssues($reporter)
            ->where('status', IssueStatus::Resolved->value)
            ->findOrFail($issueId);

        $workflow->close($issue, $reporter);

        session()->flash('workflow-status', 'Issue confirmed and closed.');
        $this->resetPage();
    }

    public function reopenIssue(int $issueId, IssueWorkflow $workflow): void
    {
        $reporter = $this->reporter();

        $this->validate([
            "reopenReasons.{$issueId}" => ['required', 'string', 'max:2000'],
        ]);

        $issue = $this->reporterIssues($reporter)
            ->whereIn('status', [IssueStatus::Resolved->value, IssueStatus::Closed->value])
            ->findOrFail($issueId);

        $workflow->reopen($issue, $reporter, $this->reopenReasons[$issueId]);

        unset($this->reopenReasons[$issueId]);
        session()->flash('workflow-status', 'Issue reopened for further work.');
        $this->resetPage();
    }

    public function render(): View
    {
        $reporter = $this->reporter();

        return view('livewire.reporter.confirmation-queue', [
            'issues' => $this->reporterIssues($reporter)
                ->with('location')
                ->whereIn('status', [IssueStatus::Resolved->value, IssueStatus::Closed->value])
                ->orderByDesc('updated_at')
                ->paginate(20),
        ]);
    }

    private function reporterIssues(User $reporter): Builder
    {
        return Issue::query()->whereHas(
            'reporters',
            static fn (Builder $query) => $query->where('users.id', $reporter->getKey()),
        );
    }

    private function reporter(): User
    {
        $actor = auth()->user();

        abort_unless($actor instanceof User, 403);

        return $actor;
    }
}
