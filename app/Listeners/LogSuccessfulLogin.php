<?php

namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Request;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        AuditLog::create([
            'User_ID' => $event->user->User_ID ?? $event->user->id,
            'Action' => 'login',
            'Auditable_Type' => 'Auth',
            'Description' => 'User logged in',
            'IP_Address' => Request::ip(),
            'User_Agent' => Request::userAgent(),
            'Created_At' => now(),
        ]);
    }
}