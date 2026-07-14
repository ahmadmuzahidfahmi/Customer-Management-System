<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leads;
use App\Models\Customer;
use App\Models\User;
use App\Models\Note;



use Illuminate\Database\Eloquent\Model;
class LeadController extends Controller
{

    public function index(Request $request)
{
    $query = Leads::query();

    // Header search bar — searches across multiple fields at once
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('Lead_Name', 'like', '%' . $search . '%')
            ->orWhere('Status', 'like', '%' . $search . '%')
              ->orWhere('Source', 'like', '%' . $search . '%');
        });
    }

    // Reserved for the future filter panel (applied on top of search, not instead of it)
    if ($request->filled('lead')) {
        $query->where('Lead_Name', 'like', '%' . $request->lead . '%');
    }

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
    
    $lead = Leads::findOrFail($id);
    

    return view('lead-view', compact('lead'));
}

public function create()
{
    $users = User::all(); // optional

    $customers = Customer::orderBy('Company_Name')->get();

    return view('lead-create', compact('users','customers'));
}

public function store(Request $request)
{
    $request->validate([
        'Lead_Name' => 'required|string|max:255',
    ]);

    Leads::create([
        'Lead_Name'        => $request->Lead_Name,
        'Source'           => $request->Source,
        'User_ID'          => $request->User_ID,
        'Status'           => $request->Status,
        'Estimated_Value'  => $request->Estimated_Value,
        'Company_ID'       => $request->Company_ID,
        'Contact_ID'       => $request->Contact_ID,
        ''
    ]);

    return redirect()
        ->route('leads')
        ->with('success', 'Lead created successfully.');
}

public function edit($id)
{
    $lead = Leads::findOrFail($id);

    $customers = Customer::orderBy('Company_Name')->get();

    $users = User::all();

    return view('lead-edit', compact(
        'lead',
        'customers',
        'users'
    ));
}

public function update(Request $request, $id)
{
    $lead = Leads::findOrFail($id);

    $lead->update([
        'Lead_Name'       => $request->Lead_Name,
        'Company_ID'      => $request->Company_ID,
        'Source'          => $request->Source,
        'User_ID'         => $request->User_ID,
        'Status'          => $request->Status,
        'Estimated_Value' => $request->Estimated_Value,
    ]);

    return redirect()
        ->route('leads.show', $lead->Lead_ID)
        ->with('success', 'Lead updated successfully.');
}
public function destroy($id)
{
    $lead = Leads::findOrFail($id);

    $lead->delete();

    return redirect()
        ->route('leads')
        ->with('success', 'Lead moved to Recycle Bin.');
}


public function restore($id)
{
    $lead = Leads::onlyTrashed()
        ->where('Lead_ID', $id)
        ->firstOrFail();

    $lead->restore();

    return back()
        ->with('success', 'Lead restored successfully.');
}

public function forceDelete($id)
{
    $lead = Leads::onlyTrashed()
        ->where('Lead_ID', $id)
        ->firstOrFail();

    $lead->forceDelete();

    return back()
        ->with('success', 'Lead permanently deleted.');
}

public function showDeleted($id)
{
    $lead = Leads::onlyTrashed()
        ->where('Lead_ID', $id)
        ->firstOrFail();

    return view('lead-view', compact('lead'));
}

public function notes()
{
    return $this->hasMany(Note::class, 'Customer_ID', 'Company_ID')->latest('Created_At');
}
}