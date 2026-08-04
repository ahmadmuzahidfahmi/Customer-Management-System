<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureGuestIsReadOnly
{
    public function handle(Request $request, Closure $next)
    {
        $isGuest = Auth::check() && Auth::user()->User_Role === 'Guest';

        if ($isGuest && !$request->isMethod('GET')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Guest accounts are read-only. Sign in with a full account to make changes.',
                ], 403);
            }

            return back()->with('guest_blocked', 'Guest accounts are read-only — sign in with a full account to make changes.');
        }

        return $next($request);
    }
}