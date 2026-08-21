<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Leads;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with(['lead', 'contact', 'assignee'])
        ->latest('Created_At');

        if ($request->filled('status')) {
            $query->where('Status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('Activity_Type', $request->type);
        }

        if ($request->boolean('mine')) {
            $query->where('Assigned_To', Auth::id());
        }

        $activities = $query->paginate(15)->withQueryString();

        $dueToday = Activity::with(['lead', 'contact', 'assignee'])
        ->where('Status', 'Pending')
        ->whereDate('Dead_Line', today())
        ->orderBy('Dead_Line')
        ->get();

        $totalActivities = Activity::count();
        $pendingActivities = Activity::where('Status', 'Pending')->count();
        $overdueActivities = Activity::where('Status', 'Pending')->where('Dead_Line', '<', now())->count();
        $completedActivities = Activity::where('Status', 'Completed')->count();

        $leads = Leads::with('company')->orderBy('Lead_Name')->get();
        $contacts = Contact::with('company')->orderBy('Contact_Name')->get();
        $users = User::orderBy('User_Name')->get();

        return view('activities.index', compact(
        'activities',
        'dueToday',
        'totalActivities',
        'pendingActivities',
        'overdueActivities',
        'completedActivities',
        'leads',
        'contacts',
        'users'
            ));
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
            return back()->withErrors([
                'Subject' => 'An activity must be linked to a Lead or a Contact.',
            ]);
        }
            $deadline = null;

        if ($request->filled('Dead_Line')) {

            if (strlen($request->Dead_Line) === 10) {

                $deadline = Carbon::parse($request->Dead_Line)
                    ->setTime(23, 59, 0);

            } else {

                $deadline = Carbon::parse($request->Dead_Line);
            }
        }

        Activity::create(array_merge($validated, [
            'Dead_Line'   => $deadline,
            'User_ID'  => Auth::id(),
            'Assigned_To' => $validated['Assigned_To'] ?? Auth::id(),
            'Status'      => 'Pending',
        ]));

        return back()->with('success', 'Activity logged.');
    }

    public function show($id)
{
    $activity = Activity::with([
        'lead',
        'contact',
        'creator',
        'assignee'
    ])->findOrFail($id);

    return view('activity-view', compact('activity'));
}

    public function update(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);

        $validated = $request->validate([
            'Activity_Type'   => 'required|in:Call,Email,Meeting,Follow-Up,Other',
            'Subject'         => 'required|string|max:255',
            'Activity_Detail' => 'nullable|string',
            'Dead_Line'       => 'nullable|date',
        ]);

        $activity->update($validated);

        return back()->with('success', 'Activity updated.');
    }

    public function complete($id)
    {
        Activity::findOrFail($id)->update([
            'Status'       => 'Completed',
            'Completed_At' => now(),
        ]);

        return back()->with('success', 'Marked as completed.');
    }

    public function cancel($id)
    {
        Activity::findOrFail($id)->update([
            'Status' => 'Cancelled',
        ]);

        return back()->with('success', 'Activity cancelled.');
    }

    public function destroy($id)
    {
        Activity::findOrFail($id)->delete();

        return back()->with('success', 'Activity deleted.');
    }
}