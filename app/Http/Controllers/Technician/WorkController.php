<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WorkController extends Controller
{
    /**
     * Display a listing of work.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $this->authorize('viewAny', $user);
        
        return view('technician.work.index', [
            'tasks' => [], // TODO: Fetch tasks assigned to technician
        ]);
    }

    /**
     * Display the specified work.
     */
    public function show(Request $request, $task)
    {
        $user = auth()->user();
        // Technician hanya boleh melihat pekerjaan yang sesuai kewenangannya
        // TODO: Implement task ownership check
        
        return view('technician.work.show', [
            'task' => $task,
        ]);
    }

    /**
     * Mark work as completed.
     */
    public function complete(Request $request, $task)
    {
        $user = auth()->user();
        $this->authorize('update', $user);
        
        // TODO: Implement completion logic
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pekerjaan berhasil diselesaikan.',
            ]);
        }

        return redirect()->route('technician.work.index')
            ->with('success', 'Pekerjaan berhasil diselesaikan.');
    }
}