<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Customer;
use App\Models\Contact;
use App\Models\Leads;



class CustomerController extends Controller
{
public function index(Request $request)
{
    $query = Customer::query();

    // Filter by company
    if ($request->filled('company')) {
        $query->where('company_name', 'like', '%' . $request->company . '%');
    }

    // Filter by status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Final result (with pagination)
    $customers = $query->paginate(10);

    return view('customers', compact('customers'));
}

public function create()
{
    return view('customer-create');
}
public function store(Request $request)
{
        Customer::create([
        'Company_Name' => $request->Company_Name,
        'Company_Email' => $request->Company_Email,
        'Company_No' => $request->Country_Code . $request->Company_No,
        'Status' => $request->Status,
        'Company_Note' => $request->Company_Note,
    ]);

    return redirect()
        ->route('customers')
        ->with('success', 'Customer added successfully.');

}

public function show($id)
{
    $customer = Customer::with('leads')->findOrFail($id);

    return view('customer-view', compact('customer'));

    $customer = Customer::with('contacts')
    ->findOrFail($id);

    return view('customer-view', compact('customer'));
}

public function edit($id)
{
    $customer = Customer::findOrFail($id);

    return view('customer-edit', compact('customer'));
}

public function update(Request $request, $id)
{
    $customer = Customer::findOrFail($id);

    $customer->update([
        'Company_Name'  => $request->Company_Name,
        'Company_Email' => $request->Company_Email,
        'Company_No'    => $request->Company_No,
        'Status'        => $request->Status,
        'Company_Note'  => $request->Company_Note,
    ]);

    return redirect()
        ->route('customers.show', $customer->Company_ID)
        ->with('success', 'Customer updated successfully.');
}

public function destroy($id)
{
    $customer = Customer::findOrFail($id);

    $customer->delete();

    return redirect()
        ->route('customers')
        ->with('success', 'Customer moved to recycle bin.');
}

public function restore($id)
{
    Customer::onlyTrashed()
        ->findOrFail($id)
        ->restore();

    return back();
}

public function recycleBin()
{
   $customers = Customer::onlyTrashed()->get();
$contacts = Contact::onlyTrashed()->get();
$deletedLeads = Leads::onlyTrashed()->get();


    return view('recycle-bin', compact('customers', 'contacts', 'deletedLeads'));
}

public function forceDelete($id)
{
    Customer::onlyTrashed()
        ->where('Company_ID', $id)
        ->forceDelete();

    return back();
}

public function showDeleted($id)
{
    $customer = Customer::onlyTrashed()
        ->where('Company_ID', $id)
        ->firstOrFail();

    return view('customer-view', compact('customer'));
}
}