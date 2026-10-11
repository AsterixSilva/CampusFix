<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use App\Policies\MemberPolicy;
use App\Policies\TechnicianPolicy;
use App\Policies\CoordinatorPolicy;
use App\Policies\AdminPolicy;
use App\Policies\SuperAdminPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Role-specific gates
        Gate::define('view-member-area', [MemberPolicy::class, 'accessAdmin']);
        Gate::define('view-admin-dashboard', [AdminPolicy::class, 'viewAny']);
        Gate::define('view-superadmin-dashboard', [SuperAdminPolicy::class, 'viewAny']);
        Gate::define('configure-system', [SuperAdminPolicy::class, 'configureSystem']);
        Gate::define('manage-users', [AdminPolicy::class, 'manageUsers']);
        Gate::define('read-technician-data', [TechnicianPolicy::class, 'viewAny']);
        Gate::define('verify-assignment', [CoordinatorPolicy::class, 'verify']);
        Gate::define('assign-work', [CoordinatorPolicy::class, 'assign']);
    }
}