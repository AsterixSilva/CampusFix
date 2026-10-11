<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TechnicianMiddleware
{
    /**
     * Handle an incoming request.
     * Technician hanya boleh melihat pekerjaan yang sesuai kewenangannya
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

        if ($user->role !== User::ROLE_TECHNICIAN && !$user->isAdmin() && !$user->isSuperAdmin()) {
            \Log::warning('Technician page unauthorized access', [
                'user_id' => $user->id,
                'role' => $user->role,
            ]);

            abort(403, 'Halaman Technician hanya dapat diakses oleh Technician, Admin, atau Super Admin.');
        }

        return $next($request);
    }
}