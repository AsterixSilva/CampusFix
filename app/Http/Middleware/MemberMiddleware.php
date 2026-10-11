<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class MemberMiddleware
{
    /**
     * Handle an incoming request.
     * Member tidak boleh mengakses halaman admin
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

        // Member hanya boleh akses area sendiri
        // Semua role bisa akses except member untuk halaman tertentu
        if ($user->role !== User::ROLE_MEMBER) {
            \Log::warning('Member area unauthorized access', [
                'user_id' => $user->id,
                'role' => $user->role,
            ]);

            // Non-member dialihkan ke halaman yang sesuai
            if ($user->role === User::ROLE_ADMIN || $user->role === User::ROLE_SUPER_ADMIN) {
                return redirect()->route('admin.dashboard');
            }
            
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}