<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    /**
     * Display system settings.
     */
    public function settings(Request $request)
    {
        $this->authorize('configureSystem', auth()->user());
        
        return view('admin.settings', [
            'settings' => [
                'app_name' => 'CampusFix',
                'app_version' => '1.0.0',
                'maintenance_mode' => false,
            ],
        ]);
    }

    /**
     * Update system settings.
     */
    public function updateSettings(Request $request)
    {
        $this->authorize('configureSystem', auth()->user());
        
        $validated = $request->validate([
            'app_name' => ['required', 'string', 'max:255'],
        ]);

        // Simpan settings ke database/file
        // TODO: Implement actual settings storage

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan berhasil diperbarui.',
            ]);
        }

        return redirect()->back()
            ->with('success', 'Pengaturan berhasil diperbarui.');
    }
}