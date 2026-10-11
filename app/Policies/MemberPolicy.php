<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesRequests;

class MemberPolicy
{
    use HandlesRequests;

    /**
     * Determine whether the user can view member data.
     * Member tidak boleh mengakses halaman admin
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        // Member tidak bisa melihat semua member via admin panel
        return $user->role === User::ROLE_MEMBER || 
               $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view a specific member.
     *
     * @param \App\Models\User $user
     * @param \App\Models\User $target
     * @return bool
     */
    public function view(User $user, User $target): bool
    {
        // Member hanya bisa melihat profile sendiri
        if ($user->role === User::ROLE_MEMBER) {
            return $user->id === $target->id;
        }

        // Super Admin bisa melihat semua
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Admin dan Coordinator bisa melihat semua member
        if ($user->isAdmin() || $user->role === User::ROLE_COORDINATOR) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can access admin area.
     * Member tidak boleh mengakses halaman admin
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function accessAdmin(User $user): bool
    {
        return !$user->isMember();
    }
}