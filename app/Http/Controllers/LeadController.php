<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leads;
use App\Models\Customer;

use Illuminate\Database\Eloquent\Model;
class LeadController extends Controller
{
public function index(Request $request)
{
    $query = Leads::query();

    if ($request->filled('status')) {
        $query->where('Status', $request->status);
    }

    $leads = $query->paginate(10);

    // KPI Data
    $totalLeads = Leads::count();
    $newLeads = Leads::where('Status', 'New')->count();
    $contactedLeads = Leads::where('Status', 'Contacted')->count();
    $wonLeads = Leads::where('Status', 'Won')->count();

    return view('leads', compact(
        'leads',
        'totalLeads',
        'newLeads',
        'contactedLeads',
        'wonLeads'
    ));
}
public function show($id)
{
    $customer = Customer::findOrFail($id);

    return view('customer-view', compact('customer'));
}
}