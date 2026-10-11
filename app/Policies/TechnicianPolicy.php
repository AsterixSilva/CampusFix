<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesRequests;

class TechnicianPolicy
{
    use HandlesRequests;

    /**
     * Determine whether the user can view technician data.
     * Technician hanya dapat melihat pekerjaan sesuai kewenangannya
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->role === User::ROLE_TECHNICIAN || 
               $user->isAdmin() || 
               $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view technician profile.
     *
     * @param \App\Models\User $user
     * @param \App\Models\User $target
     * @return bool
     */
    public function view(User $user, User $target): bool
    {
        // Admin dan Super Admin dapat melihat semua technician
        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return true;
        }

        // Coordinator dapat melihat technician
        if ($user->role === User::ROLE_COORDINATOR) {
            return true;
        }

        // Technician hanya bisa melihat profile sendiri
        if ($user->role === User::ROLE_TECHNICIAN) {
            return $user->id === $target->id;
        }

        return false;
    }
}