<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * Roles assignable through this panel. Kept as a single source of
     * truth here rather than scattering the same three strings across
     * views and validation rules.
     */
    protected array $roles = ['Admin', 'Staff', 'Guest'];

    public function store(Request $request)
    {
        $validated = $request->validate([
            'User_Name'     => ['required', 'string', 'max:40', Rule::unique('users', 'User_Name')],
            'User_Email'    => ['required', 'email', 'max:255', Rule::unique('users', 'User_Email')],
            'User_Password' => ['required', 'string', 'min:8'],
            'User_Role'     => ['required', Rule::in($this->roles)],
            'Status'        => ['required', Rule::in(['Active', 'Inactive'])],
        ]);

        User::create([
            'User_Name'     => $validated['User_Name'],
            'User_Email'    => $validated['User_Email'],
            'User_Password' => Hash::make($validated['User_Password']),
            'User_Role'     => $validated['User_Role'],
            'Status'        => $validated['Status'],
        ]);

        return redirect()
            ->route('profile')
            ->with('success', 'User created successfully.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'User_Name'     => ['required', 'string', 'max:40', Rule::unique('users', 'User_Name')->ignore($user->User_ID, 'User_ID')],
            'User_Email'    => ['required', 'email', 'max:255', Rule::unique('users', 'User_Email')->ignore($user->User_ID, 'User_ID')],
            'User_Role'     => ['required', Rule::in($this->roles)],
            'Status'        => ['required', Rule::in(['Active', 'Inactive'])],
            'User_Password' => ['nullable', 'string', 'min:8'],
        ]);

        // Guard rail: an admin can't demote themselves out of Admin through
        // this panel. Prevents accidentally locking yourself out of user
        // management with no other admin account to fix it.
        if ($user->User_ID === Auth::id() && $validated['User_Role'] !== 'Admin') {
            return back()
                ->withErrors(['User_Role' => 'You cannot remove your own Admin role.'])
                ->withInput();
        }

        $user->User_Name  = $validated['User_Name'];
        $user->User_Email = $validated['User_Email'];
        $user->User_Role  = $validated['User_Role'];
        $user->Status     = $validated['Status'];

        if (!empty($validated['User_Password'])) {
            $user->User_Password = Hash::make($validated['User_Password']);
        }

        $user->save();

        return redirect()
            ->route('profile')
            ->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->User_ID === Auth::id()) {
            return back()->withErrors(['User_ID' => 'You cannot delete your own account.']);
        }

        if ($user->User_Role === 'Admin' && User::where('User_Role', 'Admin')->count() <= 1) {
            return back()->withErrors(['User_ID' => 'You cannot delete the last remaining Admin account.']);
        }

        $user->delete();

        return redirect()
            ->route('profile')
            ->with('success', 'User deleted successfully.');
    }
}