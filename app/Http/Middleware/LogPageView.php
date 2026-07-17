<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class LogPageView
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (Auth::check() && $response->getStatusCode() === 200) {
            $routeName = $request->route()?->getName() ?? '';
            $id = collect($request->route()?->parameters())->first();

            AuditLog::create([
                'User_ID' => Auth::id(),
                'Action' => 'viewed',
                'Auditable_Type' => $this->resolveType($routeName),
                'Auditable_ID' => is_numeric($id) ? $id : null,
                'Description' => 'Viewed ' . str_replace('.', ' ', $routeName),
                'IP_Address' => $request->ip(),
                'User_Agent' => $request->userAgent(),
                'Created_At' => now(),
            ]);
        }

        return $response;
    }

    private function resolveType(string $routeName): string
    {
        return match (true) {
            str_starts_with($routeName, 'customers') => 'Customer',
            str_starts_with($routeName, 'contacts') => 'Contact',
            str_starts_with($routeName, 'leads') => 'Lead',
            default => 'Page',
        };
    }
}