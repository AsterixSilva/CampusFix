<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesRequests;

class CoordinatorPolicy
{
    use HandlesRequests;

    /**
     * Determine whether the user can view coordinator data.
     * Coordinator dapat melihat data sesuai scope
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->role === User::ROLE_COORDINATOR || 
               $user->isAdmin() || 
               $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can verify assignments.
     * Coordinator dapat memverifikasi assignment
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function verify(User $user): bool
    {
        return $user->role === User::ROLE_COORDINATOR || 
               $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can assign work.
     * Coordinator dapat melakukan assignment sesuai scope
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function assign(User $user): bool
    {
        return $user->role === User::ROLE_COORDINATOR || 
               $user->isAdmin() || 
               $user->isSuperAdmin();
    }
}