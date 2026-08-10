<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'User_Name' => 'required|string',
            'password'  => 'required|string',
            'read_only' => 'sometimes|boolean',
        ]);

        $user = User::where('User_Name', $validated['User_Name'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->User_Password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $readOnly = $request->boolean('read_only');
        $abilities = $readOnly ? ['read'] : ['read', 'write'];

        $token = $user->createToken(
            $readOnly ? 'api-token-readonly' : 'api-token',
            $abilities
        )->plainTextToken;

        return response()->json([
            'success' => true,
            'token'   => $token,
            'abilities' => $abilities,
            'user'    => [
                'id'   => $user->User_ID,
                'name' => $user->User_Name,
                'role' => $user->User_Role,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out',
        ]);
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'User_Name'  => ['required', 'string', 'max:255', 'unique:users,User_Name'],
            'User_Email' => ['required', 'email', 'max:255', 'unique:users,User_Email'],
            'password'   => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create([
            'User_Name'     => $validated['User_Name'],
            'User_Email'    => $validated['User_Email'],
            'User_Password' => Hash::make($validated['password']),
            // New self-registered accounts get the least-privileged real role.
            // Promoting someone to Admin is a deliberate separate action, not automatic.
            'User_Role'     => 'Staff',
            'Status'        => 'Active',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }
}