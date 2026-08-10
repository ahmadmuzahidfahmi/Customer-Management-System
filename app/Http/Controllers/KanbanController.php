<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leads;
use Illuminate\Support\Facades\DB;

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
        $oldStatus = $lead->Status;
        $newStatus = $request->Status;
        $newIndex = max(0, $request->Position - 1); // incoming Position is 1-based

        DB::transaction(function () use ($lead, $oldStatus, $newStatus, $newIndex) {
            // Move the dragged lead itself — this fires the model's normal
            // boot() hooks (Status_Changed_At updates correctly if the column changed).
            $lead->Status = $newStatus;
            $lead->save();

            // Renumber the destination column, inserting the lead at its dropped index.
            $columnLeads = Leads::where('Status', $newStatus)
                ->where('Lead_ID', '!=', $lead->Lead_ID)
                ->orderBy('Position')
                ->get()
                ->values();

            $columnLeads->splice($newIndex, 0, [$lead]);

            foreach ($columnLeads as $i => $l) {
                $l->Position = $i + 1;

                if ($l->Lead_ID === $lead->Lead_ID) {
                    $l->save(); // the dragged card: normal save, keep its real timestamps
                } else {
                    // Siblings are just being renumbered, not meaningfully "updated" —
                    // skip events/timestamps so their Updated_At doesn't change
                    // just because a neighbor was dragged past them.
                    $l->timestamps = false;
                    $l->saveQuietly();
                }
            }

            // If the lead changed columns, close the gap left behind in the old column.
            if ($oldStatus !== $newStatus) {
                $oldColumnLeads = Leads::where('Status', $oldStatus)->orderBy('Position')->get();

                foreach ($oldColumnLeads as $i => $l) {
                    $l->Position = $i + 1;
                    $l->timestamps = false;
                    $l->saveQuietly();
                }
            }
        });

        return response()->json(['success' => true]);
    }
}