<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Leads;

class DashboardController extends Controller
{
    public function index()
    {
        $totalCustomers = Customer::count();

        $totalLeads = Leads::count();

        $wonLeads = Leads::where('Status', 'Won')->count();

        $lostLeads = Leads::where('Status', 'Lost')->count();

        $qualifiedLeads = Leads::where('Status', 'Qualified')->count();

        $contactedleads = Leads::where('Status', 'Contacted')->count();

        $recentCustomers = Customer::orderBy('Closed_Date', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalCustomers',
            'totalLeads',
            'wonLeads',
            'lostLeads',
            'qualifiedLeads',
            'contactedleads',
            'recentCustomers' // 👈 REQUIRED FIX
        ));
    }
}