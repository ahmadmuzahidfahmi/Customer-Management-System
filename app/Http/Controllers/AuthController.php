<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'User_Name' => ['required', 'string'],
            'password'  => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Record last login without disturbing Updated_At
            $user = Auth::user();
            $user->timestamps = false;
            $user->Last_Login = now();
            $user->save();

            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withErrors(['User_Name' => 'These credentials do not match our records.'])
            ->onlyInput('User_Name');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

        public function guestLogin(Request $request)
    {
        $guest = User::firstOrCreate(
            ['User_Name' => 'guest'],
            [
                'User_Email'    => 'guest@example.com',
                // Random, unusable password — guests never log in with a password.
                'User_Password' => Hash::make(Str::random(40)),
                'User_Role'     => 'Guest',
                'Status'        => 'Active',
            ]
        );

        Auth::login($guest);
        $request->session()->regenerate();

        $guest->timestamps = false;
        $guest->Last_Login = now();
        $guest->save();

        return redirect()->intended(route('dashboard'));
    }
}