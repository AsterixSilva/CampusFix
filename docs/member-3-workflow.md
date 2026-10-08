# Member 3: issue workflow

This branch owns the issue status machine and append-only status history.

## Status transitions

- reported -> verified or rejected
- verified -> assigned or rejected
- assigned -> in_progress
- in_progress -> on_hold or resolved
- on_hold -> assigned or in_progress
- resolved -> closed or reopened
- closed -> reopened
- reopened -> verified
- rejected and merged are terminal

The merged state is reserved for the later duplicate-merge step. It is not reachable until the team confirms how the source issue links to its surviving issue.

## Integration expectations

- The shared issue model is `App\\Models\\Issue` and stores the current status in a `status` column.
- The shared user model is `App\\Models\\User` and exposes a string `role` value.
- The issue model exposes a `reporters()` relationship for members of the `issue_user` pivot with reporter membership.
- The assignments table uses `issue_id`, `technician_id`, `accepted_at`, and `released_at`.
- The reporting service calls `IssueWorkflow::recordInitialReported()` after creating a reported issue.
- Every later status change goes through `IssueWorkflow::transition()`; controllers and Livewire components do not write `issues.status` directly.
- The status history migration depends on the shared `issues` and `users` tables existing first.

The service locks the issue row during a transition, authorizes through `IssuePolicy`, records the actor and reason, and emits `IssueStatusChanged` after the surrounding database transaction commits.
