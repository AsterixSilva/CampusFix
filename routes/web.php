<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Route authentication dan authorization dengan role-based access
|
*/

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.post');

// Protected routes - require authentication
Route::middleware(['auth'])->group(function () {
    
    // Logout route
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Member routes (default dashboard)
    Route::get('/member/dashboard', [DashboardController::class, 'member'])->name('member.dashboard')
        ->middleware('role:member');

    // Technician routes - hanya boleh melihat pekerjaan sesuai kewenangannya
    Route::get('/technician/dashboard', [DashboardController::class, 'technician'])->name('technician.dashboard')
        ->middleware('role:technician,admin,super_admin');

    // Coordinator routes - dapat memverifikasi dan melakukan assignment
    Route::get('/coordinator/dashboard', [DashboardController::class, 'coordinator'])->name('coordinator.dashboard')
        ->middleware('role:coordinator,admin,super_admin');

    // Admin routes - dapat melihat data sesuai scope
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard')
        ->middleware('admin');

    // Super Admin routes - memiliki akses konfigurasi sistem
    Route::get('/superadmin/dashboard', [DashboardController::class, 'superadmin'])->name('superadmin.dashboard')
        ->middleware('superadmin');

    // Admin management routes
    Route::prefix('admin')->name('admin.')->group(function () {
        // User management
        Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
        Route::post('/users', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
        
        // System configuration (Super Admin only)
        Route::get('/settings', [App\Http\Controllers\Admin\SystemController::class, 'settings'])->name('settings')
            ->middleware('superadmin');
        Route::put('/settings', [App\Http\Controllers\Admin\SystemController::class, 'updateSettings'])->name('settings.update')
            ->middleware('superadmin');
    });

    // Super Admin only routes
    Route::prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('/users', [App\Http\Controllers\SuperAdmin\UserController::class, 'index'])->name('users.index');
        Route::get('/roles', [App\Http\Controllers\SuperAdmin\RoleController::class, 'index'])->name('roles.index');
        Route::post('/roles', [App\Http\Controllers\SuperAdmin\RoleController::class, 'store'])->name('roles.store');
        Route::put('/roles/{role}', [App\Http\Controllers\SuperAdmin\RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [App\Http\Controllers\SuperAdmin\RoleController::class, 'destroy'])->name('roles.destroy');
    });

    // Coordinator routes
    Route::prefix('coordinator')->name('coordinator.')->group(function () {
        Route::get('/assignments', [App\Http\Controllers\Coordinator\AssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/create', [App\Http\Controllers\Coordinator\AssignmentController::class, 'create'])->name('assignments.create');
        Route::post('/assignments', [App\Http\Controllers\Coordinator\AssignmentController::class, 'store'])->name('assignments.store');
        Route::get('/assignments/{assignment}/verify', [App\Http\Controllers\Coordinator\AssignmentController::class, 'verify'])->name('assignments.verify');
        Route::put('/assignments/{assignment}/approve', [App\Http\Controllers\Coordinator\AssignmentController::class, 'approve'])->name('assignments.approve');
    });

    // Technician routes
    Route::prefix('technician')->name('technician.')->group(function () {
        Route::get('/work', [App\Http\Controllers\Technician\WorkController::class, 'index'])->name('work.index');
        Route::get('/work/{task}', [App\Http\Controllers\Technician\WorkController::class, 'show'])->name('work.show');
        Route::put('/work/{task}/complete', [App\Http\Controllers\Technician\WorkController::class, 'complete'])->name('work.complete');
    });

    // Member routes
    Route::prefix('member')->name('member.')->group(function () {
        Route::get('/profile', [App\Http\Controllers\Member\ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [App\Http\Controllers\Member\ProfileController::class, 'edit'])->name('profile.edit');
    });
});