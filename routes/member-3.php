<?php

declare(strict_types=1);

use App\Livewire\Coordinator\IssueReviewQueue;
use App\Livewire\Technician\IssueQueue;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::get('/coordinator/issues/review', IssueReviewQueue::class)
        ->name('coordinator.issues.review');

    Route::get('/technician/issues', IssueQueue::class)
        ->name('technician.issues.index');
});
