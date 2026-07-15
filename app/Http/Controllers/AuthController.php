<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}