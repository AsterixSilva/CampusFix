# Member 3: issue and technician workflow

This branch owns the issue status machine, verification/rejection, assignments, technician work, and append-only status/resolution history.

## Status transitions

- reported -> verified or rejected
- verified -> assigned or rejected
- assigned -> in_progress
- in_progress -> on_hold or resolved
- on_hold -> in_progress
- resolved -> closed or reopened
- closed -> reopened
- reopened -> verified
- rejected and merged are terminal

The merged state is reserved for the later duplicate-merge step. It is not reachable until the team confirms how the source issue links to its surviving issue. A non-empty reason is required for rejected, on_hold, and reopened transitions.

## Integration expectations

- The shared issue model is `App\\Models\\Issue` and stores the current status in a `status` column.
- The shared user model is `App\\Models\\User` and exposes a string `role` value.
- The issue model exposes a `reporters()` relationship for reporter membership in `issue_user`.
- The `team_user` pivot uses `team_id` and `user_id`; technicians assigned to an issue must belong to the selected team.
- The assignments table records `issue_id`, `team_id`, `technician_id`, `assigned_by`, `assigned_at`, `accepted_at`, and `released_at`.
- Progress updates use the shared `comments` table with `issue_id`, `user_id`, `body`, `created_at`, and `updated_at`.
- Resolutions record `issue_id`, `assignment_id`, `resolved_by`, `root_cause`, `action_taken`, `parts_used`, and `minutes_spent`.
- The reporting service calls `IssueWorkflow::recordInitialReported()` after creating a reported issue and adding the reporter relation.
- Verification, rejection, assignment, acceptance, work updates, and status changes go through `IssueWorkflow`; UI components do not write workflow data directly.
- Include `routes/member-3.php` from the app's authenticated web routes.
- Migrations depend on the shared `issues`, `users`, and `teams` tables; assignments must be created before resolutions.

The workflow locks the issue row during state changes, authorizes through `IssuePolicy`, records the actor and reason, and emits `IssueStatusChanged` after the surrounding transaction commits.
