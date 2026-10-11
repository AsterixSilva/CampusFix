<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);
        
        $users = User::with('role')
            ->when($request->search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->paginate(15);
        
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(Request $request)
    {
        $this->authorize('create', User::class);
        
        return view('admin.users.create', [
            'roles' => User::getRoles(),
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', 'min:8'],
            'role' => ['required', 'in:' . implode(',', User::getRoles())],
            'is_active' => ['boolean'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengguna berhasil dibuat.',
                'user' => $user,
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dibuat.');
    }

    /**
     * Display the specified user.
     */
    public function show(Request $request, User $user)
    {
        $this->authorize('view', $user);
        
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(Request $request, User $user)
    {
        $this->authorize('update', $user);
        
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => User::getRoles(),
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'in:' . implode(',', User::getRoles())],
            'is_active' => ['boolean'],
        ]);

        $user->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengguna berhasil diperbarui.',
                'user' => $user,
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, User $user)
    {
        $this->authorize('delete', $user);
        
        $user->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengguna berhasil dihapus.',
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}