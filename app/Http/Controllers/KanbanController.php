<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leads;

class KanbanController extends Controller
{
    protected array $statuses = ['New', 'Contacted', 'Qualified', 'Won', 'Lost'];

    public function index()
    {
        $leads = Leads::orderBy('Position')->get()->groupBy('Status');

        return view('kanban', [
            'statuses' => $this->statuses,
            'leadsByStatus' => $leads,
        ]);
    }

    public function updatePosition(Request $request)
    {
        $request->validate([
            'Lead_ID' => 'required|exists:leads,Lead_ID',
            'Status' => 'required|string',
            'Position' => 'required|integer',
        ]);

        $lead = Leads::findOrFail($request->Lead_ID);
        $lead->Status = $request->Status;
        $lead->Position = $request->Position;
        $lead->save();

        return response()->json(['success' => true]);
    }
}