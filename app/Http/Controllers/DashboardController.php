<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Member dashboard.
     * Member tidak boleh mengakses halaman admin.
     */
    public function member(Request $request)
    {
        $user = $request->user();
        
        return view('dashboards.member', [
            'title' => 'Dashboard Member',
            'user' => $user,
            'roles' => \App\Models\User::getRoles(),
        ]);
    }

    /**
     * Technician dashboard.
     * Technician hanya boleh melihat pekerjaan yang sesuai kewenangannya.
     */
    public function technician(Request $request)
    {
        $user = $request->user();
        
        return view('dashboards.technician', [
            'title' => 'Dashboard Technician',
            'user' => $user,
            'tasks' => [], // TODO: Fetch technician tasks
        ]);
    }

    /**
     * Coordinator dashboard.
     * Coordinator dapat memverifikasi dan melakukan assignment sesuai scope.
     */
    public function coordinator(Request $request)
    {
        $user = $request->user();
        
        return view('dashboards.coordinator', [
            'title' => 'Dashboard Coordinator',
            'user' => $user,
            'assignments' => [], // TODO: Fetch assignments
        ]);
    }

    /**
     * Admin dashboard.
     * Admin dapat melihat data sesuai scope.
     */
    public function admin(Request $request)
    {
        $user = $request->user();
        
        return view('dashboards.admin', [
            'title' => 'Dashboard Admin',
            'user' => $user,
            'users' => \App\Models\User::all(),
        ]);
    }

    /**
     * Super Admin dashboard.
     * Super Admin memiliki akses konfigurasi sistem.
     */
    public function superadmin(Request $request)
    {
        $user = $request->user();
        
        return view('dashboards.superadmin', [
            'title' => 'Dashboard Super Admin',
            'user' => $user,
            'settings' => [], // TODO: Fetch system settings
        ]);
    }
}