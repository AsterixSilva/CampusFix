# CampusFix

CampusFix tracks campus facility issues from report through verification, technician work, resolution, and reporter confirmation.

## Stack

- Laravel and PHP 8.3+
- MySQL 8
- Blade, Livewire, Alpine.js, Tailwind CSS
- Eloquent, Laravel Policies, Notifications, and Scheduler
- Database queue for development

## Issue lifecycle

`reported → verified → assigned → in_progress → resolved → closed`

Additional states are `rejected`, `on_hold`, `reopened`, and `merged`. Status changes are handled through the issue workflow service and recorded in append-only status history.

## Team workflow

`main` is the integration branch. Each member develops their assigned work on a separate branch for later integration.
