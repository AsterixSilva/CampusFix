<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', auth()->user());
        
        return view('superadmin.roles.index', [
            'roles' => [
                ['name' => 'member', 'label' => 'Member', 'description' => 'Akses dasar'],
                ['name' => 'technician', 'label' => 'Technician', 'description' => 'Lihat pekerjaan sesuai kewenangannya'],
                ['name' => 'coordinator', 'label' => 'Coordinator', 'description' => 'Verifikasi dan assignment'],
                ['name' => 'admin', 'label' => 'Admin', 'description' => 'Kelola data sesuai scope'],
                ['name' => 'super_admin', 'label' => 'Super Admin', 'description' => 'Akses konfigurasi sistem'],
            ],
        ]);
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('grantRoles', auth()->user());
        
        // TODO: Implement role creation logic
        return back()->with('success', 'Role berhasil dibuat.');
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, $role)
    {
        $this->authorize('grantRoles', auth()->user());
        
        // TODO: Implement role update logic
        return back()->with('success', 'Role berhasil diperbarui.');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Request $request, $role)
    {
        $this->authorize('grantRoles', auth()->user());
        
        // TODO: Implement role deletion logic
        return back()->with('success', 'Role berhasil dihapus.');
    }
}