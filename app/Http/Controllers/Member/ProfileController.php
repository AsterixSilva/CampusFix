<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display member profile.
     */
    public function show(Request $request)
    {
        $user = auth()->user();
        $this->authorize('view', $user);
        
        return view('member.profile.show', [
            'user' => $user,
        ]);
    }

    /**
     * Show edit profile form.
     */
    public function edit(Request $request)
    {
        $user = auth()->user();
        $this->authorize('update', $user);
        
        return view('member.profile.edit', [
            'user' => $user,
        ]);
    }
}