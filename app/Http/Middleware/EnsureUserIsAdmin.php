<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsAdmin
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->Role !== 'Admin') {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}