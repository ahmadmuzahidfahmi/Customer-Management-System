<?php

namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Request;

class LogSuccessfulLogout
{
    public function handle(Logout $event): void
    {
        AuditLog::create([
            'User_ID' => $event->user->User_ID ?? $event->user->id ?? null,
            'Action' => 'logout',
            'Auditable_Type' => 'Auth',
            'Description' => 'User logged out',
            'IP_Address' => Request::ip(),
            'User_Agent' => Request::userAgent(),
            'Created_At' => now(),
        ]);
    }
}