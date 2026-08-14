<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Leads;
use App\Models\Activity;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        $myLeads = Leads::with('company')
            ->where('User_ID', $user->User_ID)
            ->whereNotIn('Status', ['Won', 'Lost'])
            ->orderBy('Updated_At', 'desc')
            ->take(5)
            ->get();

        $myPendingActivities = Activity::with(['lead', 'contact'])
            ->where('Assigned_To', $user->User_ID)
            ->where('Status', 'Pending')
            ->orderBy('Dead_Line', 'asc')
            ->take(5)
            ->get();

        return view('profile', [
            'user' => $user,
            'myLeads' => $myLeads,
            'myPendingActivities' => $myPendingActivities,
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