<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile', [
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'User_Name' => [
                'required', 'string', 'max:40',
                Rule::unique('users', 'User_Name')->ignore($user->User_ID, 'User_ID'),
            ],
            'User_Email' => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'User_Email')->ignore($user->User_ID, 'User_ID'),
            ],
        ]);

        $user->User_Name = $validated['User_Name'];
        $user->User_Email = $validated['User_Email'];
        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required'],
            'new_password'     => ['required', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], $user->User_Password)) {
            return back()->withErrors(['current_password' => 'Your current password is incorrect.']);
        }

        $user->User_Password = $validated['new_password']; // auto-hashed by the 'hashed' cast
        $user->save();

        return back()->with('success', 'Password changed successfully.');
    }
}