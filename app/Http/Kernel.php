<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The App's global HTTP middleware priority list.
     *
     * These middleware are run during every HTTP request to the application.
     *
     * @var array<int, class-string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustProxies::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The App's route middleware groups.
     *
     * @var array<string, array<int, class-string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Http\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\View\Middleware\EnqueueWebhooks::class,
            \App\Http\Middleware\CheckForMaintenanceMode::class,
        ],

        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The App's route middleware.
     *
     * Middleware may be assigned to routes, groups, or individually.
     *
     * @var array<string, class-string>
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // Role-based middleware
        'role' => \App\Http\Middleware\RoleMiddleware::class,
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'superadmin' => \App\Http\Middleware\SuperAdminMiddleware::class,
        'technician' => \App\Http\Middleware\TechnicianMiddleware::class,
        'coordinator' => \App\Http\Middleware\CoordinatorMiddleware::class,
        'member' => \App\Http\Middleware\MemberMiddleware::class,
    ];
}