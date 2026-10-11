<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    /**
     * Display a listing of assignments.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', auth()->user());
        
        return view('coordinator.assignments.index', [
            'assignments' => [],
        ]);
    }

    /**
     * Show the form for creating a new assignment.
     */
    public function create(Request $request)
    {
        $this->authorize('create', auth()->user());
        
        return view('coordinator.assignments.create', [
            'users' => \App\Models\User::all(),
        ]);
    }

    /**
     * Store a newly created assignment in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('assign', auth()->user());
        
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'assigned_to' => ['required', 'integer', 'exists:users,id'],
            'due_date' => ['nullable', 'date'],
        ]);

        // TODO: Implement assignment creation
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Assignment berhasil dibuat.',
            ]);
        }

        return redirect()->route('coordinator.assignments.index')
            ->with('success', 'Assignment berhasil dibuat.');
    }

    /**
     * Verify assignment status.
     */
    public function verify(Request $request, $assignment)
    {
        $this->authorize('verify', auth()->user());
        
        return view('coordinator.assignments.verify', [
            'assignment' => $assignment,
        ]);
    }

    /**
     * Approve assignment.
     */
    public function approve(Request $request, $assignment)
    {
        $this->authorize('assign', auth()->user());
        
        // TODO: Implement approval logic
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Assignment berhasil disetujui.',
            ]);
        }

        return redirect()->route('coordinator.assignments.index')
            ->with('success', 'Assignment berhasil disetujui.');
    }
}