<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesRequests;

class AdminPolicy
{
    use HandlesRequests;

    /**
     * Determine whether the user can view admin data.
     * Admin dapat melihat data sesuai scope
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->role === User::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can access system configuration.
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
     * Determine whether the user can manage users.
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function manageUsers(User $user): bool
    {
        return $user->isSuperAdmin() || $user->role === User::ROLE_ADMIN;
    }
}