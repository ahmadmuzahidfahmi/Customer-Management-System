<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LeadResource;
use App\Models\Leads;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index()
    {
        $leads = Leads::latest('Created_At')->paginate(15);

        return LeadResource::collection($leads);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Lead_Name'       => 'required|string|max:255',
            'Company_ID'      => 'required|exists:company,Company_ID',
            'Estimated_Value' => 'nullable|numeric',
            'Source'          => 'nullable|string|max:255',
            'Status'          => 'required|in:New,Contacted,Qualified,Won,Lost',
        ]);

        $lead = Leads::create($validated);

        return response()->json([
            'success' => true,
            'data'    => new LeadResource($lead),
            'message' => 'Lead created successfully',
        ], 201);
    }

    public function show($id)
    {
        $lead = Leads::find($id);

        if (! $lead) {
            return response()->json([
                'success' => false,
                'message' => 'Lead not found',
            ], 404);
        }

        return new LeadResource($lead);
    }

    public function update(Request $request, $id)
    {
        $lead = Leads::find($id);

        if (! $lead) {
            return response()->json([
                'success' => false,
                'message' => 'Lead not found',
            ], 404);
        }

        $validated = $request->validate([
            'Lead_Name'       => 'sometimes|required|string|max:255',
            'Company_ID'      => 'sometimes|required|exists:company,Company_ID',
            'Estimated_Value' => 'nullable|numeric',
            'Source'          => 'nullable|string|max:255',
            'Status'          => 'sometimes|required|in:New,Contacted,Qualified,Won,Lost',
        ]);

        $lead->update($validated);

        return response()->json([
            'success' => true,
            'data'    => new LeadResource($lead),
            'message' => 'Lead updated successfully',
        ]);
    }

    public function destroy($id)
    {
        $lead = Leads::find($id);

        if (! $lead) {
            return response()->json([
                'success' => false,
                'message' => 'Lead not found',
            ], 404);
        }

        $lead->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lead deleted successfully',
        ]);
    }
}