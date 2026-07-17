<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->orderByDesc('Created_At');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('Description', 'like', "%{$search}%")
                  ->orWhere('Auditable_Type', 'like', "%{$search}%");
            });
        }

        if ($request->filled('action')) {
            $query->where('Action', $request->action);
        }

        if ($request->filled('type')) {
            $query->where('Auditable_Type', $request->type);
        }

        $logs = $query->paginate(25)->withQueryString();

        return view('audit-log', compact('logs'));
    }
}