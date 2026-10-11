<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesRequests;

class SuperAdminPolicy
{
    use HandlesRequests;

    /**
     * Determine whether the user can view super admin data.
     * Super Admin memiliki akses konfigurasi sistem
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view super admin profile.
     *
     * @param \App\Models\User $user
     * @param \App\Models\User $target
     * @return bool
     */
    public function view(User $user, User $target): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can configure system.
     * Super Admin memiliki akses konfigurasi sistem
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function configureSystem(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can grant roles.
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function grantRoles(User $user): bool
    {
        return $user->isSuperAdmin();
    }
}