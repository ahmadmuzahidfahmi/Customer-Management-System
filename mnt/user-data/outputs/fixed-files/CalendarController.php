<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Leads;
use App\Models\Contact;
use App\Models\User;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);

        $current = Carbon::create($year, $month, 1);

        $activities = Activity::with(['lead', 'contact'])
            ->whereMonth('Dead_Line', $month)
            ->whereYear('Dead_Line', $year)
            ->get()
            ->groupBy(fn ($activity) => $activity->Dead_Line->format('Y-m-d'));

        // Weekly View — defaults to the real current week, but can be
        // navigated independently via the ?week= query param (Y-m-d of
        // any day in the target week).
        $weekParam = $request->get('week');
        $weekStart = $weekParam
            ? Carbon::parse($weekParam)->startOfWeek()
            : now()->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();

        $weeklyActivities = Activity::with(['lead', 'contact'])
            ->whereDate('Dead_Line', '>=', $weekStart->toDateString())
            ->whereDate('Dead_Line', '<=', $weekEnd->toDateString())
            ->orderBy('Dead_Line')
            ->get();

        return view('calendar', [
            'current' => $current,
            'activities' => $activities,
            'prevMonth' => $current->copy()->subMonth(),
            'nextMonth' => $current->copy()->addMonth(),

            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'prevWeek' => $weekStart->copy()->subWeek()->toDateString(),
            'nextWeek' => $weekStart->copy()->addWeek()->toDateString(),
            'weeklyActivities' => $weeklyActivities,

            // For the "New Activity" modal
            'leads' => Leads::orderBy('Lead_Name')->get(),
            'contacts' => Contact::orderBy('Contact_Name')->get(),
            'users' => User::orderBy('User_Name')->get(),
        ]);
    }

    public function reschedule(Request $request)
    {
        $request->validate([
            'Activity_ID' => 'required|exists:activities,Activity_ID',
            'Dead_Line' => 'required|date',
        ]);

        $activity = Activity::findOrFail($request->Activity_ID);
        $activity->Dead_Line = $request->Dead_Line;
        $activity->save();

        return response()->json(['success' => true]);
    }

    public function week(Request $request)
{
    $start = Carbon::parse(
        $request->week ?? now()
    )->startOfWeek();

    $end = $start->copy()->endOfWeek();

    $activities = Activity::with([
        'lead',
        'contact',
        'assignee'
    ])
    ->whereBetween('Dead_Line', [$start, $end])
    ->orderBy('Dead_Line')
    ->get();

    return view('calendar-week', [
        'start' => $start,
        'end' => $end,
        'activities' => $activities,
    ]);
}
}