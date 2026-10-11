<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;

Route::get('/', fn () => auth()->check()
    ? redirect()->route('dashboard')
    : redirect()->route('login'));

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

Route::middleware(['auth', 'active'])->group(function (): void {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])
        ->name('notifications.read');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/member/dashboard', [DashboardController::class, 'member'])
        ->middleware('role:member')->name('member.dashboard');
    Route::get('/technician/dashboard', [DashboardController::class, 'technician'])
        ->middleware('role:technician,admin,super_admin')->name('technician.dashboard');
    Route::get('/coordinator/dashboard', [DashboardController::class, 'coordinator'])
        ->middleware('role:coordinator,admin,super_admin')->name('coordinator.dashboard');
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->middleware('role:admin,super_admin')->name('admin.dashboard');
    Route::get('/superadmin/dashboard', [DashboardController::class, 'superadmin'])
        ->middleware('role:super_admin')->name('superadmin.dashboard');

    Route::livewire('/issues/create', 'issues.create-issue')
        ->middleware('role:member')->name('issues.create');
    Route::livewire('/issues/{issue}', 'issues.show-issue')->name('issues.show');

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
        Route::post('/users', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    });

    Route::middleware('superadmin')->prefix('superadmin')->name('superadmin.')->group(function (): void {
        Route::get('/users', [App\Http\Controllers\SuperAdmin\UserController::class, 'index'])->name('users.index');
        Route::get('/roles', [App\Http\Controllers\SuperAdmin\RoleController::class, 'index'])->name('roles.index');
    });
});

require __DIR__.'/member-3.php';
