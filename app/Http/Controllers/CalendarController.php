<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);

        $current = Carbon::create($year, $month, 1);

        $activities = Activity::with(['lead', 'contact'])
            ->inMonth($year, $month)
            ->get()
            ->groupBy(fn($a) => $a->Dead_Line->format('Y-m-d'));

        return view('calendar', [
            'current' => $current,
            'activities' => $activities,
            'prevMonth' => $current->copy()->subMonth(),
            'nextMonth' => $current->copy()->addMonth(),
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
}