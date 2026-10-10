<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CoordinatorMiddleware
{
    /**
     * Handle an incoming request.
     * Coordinator dapat memverifikasi dan melakukan assignment sesuai scope
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Coordinator, Admin, dan Super Admin boleh akses
        $allowedRoles = [User::ROLE_COORDINATOR, User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN];
        
        if (!in_array($user->role, $allowedRoles)) {
            \Log::warning('Coordinator page unauthorized access', [
                'user_id' => $user->id,
                'role' => $user->role,
            ]);

            abort(403, 'Halaman ini hanya dapat diakses oleh Coordinator, Admin, atau Super Admin.');
        }

        return $next($request);
    }
}