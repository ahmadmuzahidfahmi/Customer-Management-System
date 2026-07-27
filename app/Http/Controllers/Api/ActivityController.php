<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with(['lead', 'contact', 'assignee'])
            ->latest('Activity_ID');

        if ($request->filled('status')) {
            $query->where('Status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('Activity_Type', $request->type);
        }

        if ($request->filled('contact_id')) {
            $query->where('Contact_ID', $request->contact_id);
        }

        if ($request->filled('lead_id')) {
            $query->where('Lead_ID', $request->lead_id);
        }

        $activities = $query->paginate(15)->withQueryString();

        return ActivityResource::collection($activities);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Contact_ID'      => 'nullable|exists:contacts,Contact_ID',
            'Lead_ID'         => 'nullable|exists:leads,Lead_ID',
            'Activity_Type'   => 'required|in:Call,Email,Meeting,Follow-Up,Other',
            'Subject'         => 'required|string|max:255',
            'Activity_Detail' => 'nullable|string',
            'Dead_Line'       => 'nullable|date',
            'Assigned_To'     => 'nullable|exists:users,User_ID',
        ]);

        if (empty($validated['Contact_ID']) && empty($validated['Lead_ID'])) {
            return response()->json([
                'success' => false,
                'message' => 'An activity must be linked to a Lead or a Contact.',
            ], 422);
        }

        $deadline = null;

        if ($request->filled('Dead_Line')) {
            $deadline = strlen($request->Dead_Line) === 10
                ? Carbon::parse($request->Dead_Line)->setTime(23, 59, 0)
                : Carbon::parse($request->Dead_Line);
        }

        $activity = Activity::create(array_merge($validated, [
            'Dead_Line'   => $deadline,
            'User_ID'     => $request->user()->User_ID,
            'Assigned_To' => $validated['Assigned_To'] ?? $request->user()->User_ID,
            'Status'      => 'Pending',
        ]));

        return response()->json([
            'success' => true,
            'data'    => new ActivityResource($activity),
            'message' => 'Activity created successfully',
        ], 201);
    }

    public function show($id)
    {
        $activity = Activity::with(['lead', 'contact', 'creator', 'assignee'])->find($id);

        if (! $activity) {
            return response()->json([
                'success' => false,
                'message' => 'Activity not found',
            ], 404);
        }

        return new ActivityResource($activity);
    }

    public function update(Request $request, $id)
    {
        $activity = Activity::find($id);

        if (! $activity) {
            return response()->json([
                'success' => false,
                'message' => 'Activity not found',
            ], 404);
        }

        $validated = $request->validate([
            'Activity_Type'   => 'sometimes|required|in:Call,Email,Meeting,Follow-Up,Other',
            'Subject'         => 'sometimes|required|string|max:255',
            'Activity_Detail' => 'nullable|string',
            'Dead_Line'       => 'nullable|date',
            'Status'          => 'sometimes|required|in:Pending,Completed,Cancelled',
        ]);

        $activity->update($validated);

        return response()->json([
            'success' => true,
            'data'    => new ActivityResource($activity),
            'message' => 'Activity updated successfully',
        ]);
    }

    public function destroy($id)
    {
        $activity = Activity::find($id);

        if (! $activity) {
            return response()->json([
                'success' => false,
                'message' => 'Activity not found',
            ], 404);
        }

        $activity->delete();

        return response()->json([
            'success' => true,
            'message' => 'Activity deleted successfully',
        ]);
    }
}