<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Leads;
use App\Models\Activity;

class DashboardController extends Controller
{
    
    public function index()
    {

        $totalCustomers = Customer::count();

        $totalLeads = Leads::count();

        $staleLeads = Leads::with('company')->stale(7)->orderBy('Status_Changed_At')->get();

        $wonLeads = Leads::where('Status', 'Won')->count();

        $lostLeads = Leads::where('Status', 'Lost')->count();

        $qualifiedLeads = Leads::where('Status', 'Qualified')->count();

        $contactedleads = Leads::where('Status', 'Contacted')->count();

        $recentCustomers = Customer::orderBy('Closed_Date', 'desc')
            ->take(5)
            ->get();

        $upcomingFollowUps = Activity::with(['lead', 'contact'])
            ->where('Status', 'Pending')
            ->whereNotNull('Dead_Line')
            ->orderBy('Dead_Line', 'asc')
            ->take(5)
            ->get();

        $recentActivities = Activity::with(['lead', 'contact', 'creator'])
            ->latest('Created_At')
            ->take(5)
            ->get();
        
        // Customer growth: cumulative total, month by month, last 12 months
    $months = collect(range(11, 0))->map(function ($i) {
        return now()->subMonths($i)->startOfMonth();
        
    });

    $baselineCount = Customer::where('Created_At', '<', $months->first())->count();

    $growthLabels = [];
    $growthData = [];
    $runningTotal = $baselineCount;

    foreach ($months as $monthStart) {
        $monthEnd = $monthStart->copy()->endOfMonth();

        $addedThisMonth = Customer::whereBetween('Created_At', [$monthStart, $monthEnd])->count();
        $runningTotal += $addedThisMonth;

        $growthLabels[] = $monthStart->format('M Y');
        $growthData[] = $runningTotal;
    }

return view('dashboard', compact(
    'totalCustomers',
    'totalLeads',
    'staleLeads',
    'wonLeads',
    'lostLeads',
    'qualifiedLeads',
    'contactedleads',
    'recentCustomers',
    'upcomingFollowUps',
    'recentActivities',
    'growthLabels',
    'growthData'
));
    }
}