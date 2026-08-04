<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Customer;
use App\Models\Contact;
use App\Models\Leads;
use App\Models\Note;



class CustomerController extends Controller
{
public function index(Request $request)
{
    $query = Customer::query();

    // Search
    if ($request->filled('search')) {

        $search = $request->search;

        $query->where(function ($q) use ($search) {

            $q->where('Company_Name', 'like', "%{$search}%")
              ->orWhere('Status', 'like', "%{$search}%")
              ->orWhere('Company_Email', 'like', "%{$search}%")
              ->orWhere('Company_No', 'like', "%{$search}%");

        });
    }

    // Status filter
    if ($request->filled('status')) {

        $query->where('Status', $request->status);

    }

    // Sorting
    switch ($request->get('sort', 'newest')) {

        case 'oldest':
            $query->orderBy('Created_At', 'asc');
            break;

        case 'name_asc':
            $query->orderBy('Company_Name', 'asc');
            break;

        case 'name_desc':
            $query->orderBy('Company_Name', 'desc');
            break;

        default:
            $query->orderBy('Created_At', 'desc');
            break;
    }

    $customers = $query
        ->paginate(10)
        ->withQueryString();

    // Dashboard cards
    $customersCount = Customer::count();

    $activeCustomers = Customer::where('Status', 'Active')->count();

    $inactiveCustomers = Customer::where('Status', 'Inactive')->count();

    return view('customers', compact(
        'customers',
        'customersCount',
        'activeCustomers',
        'inactiveCustomers'
    ));
}

public function create()
{
    $countries = config('countries');

    return view('customer-create', compact('countries'));
}

public function store(Request $request)
{
    $validated = $request->validate([

        'Company_Name' => 'required|string|max:255',

        'Company_Email' => 'nullable|email|max:255',

        'Country_Code' => 'required|string|max:10',

        'Company_No' => 'required|string|max:20',

        'Status' => 'required|string',

    ]);

$customer = Customer::create($validated);

    if ($request->filled('Content')) {
        Note::create([
            'Subject'    => $request->input('Subject'),
            'Content'    => $request->input('Content'),
            'Company_ID' => $customer->Company_ID,
        ]);
    }

    return redirect()
        ->route('customers')
        ->with('success', 'Customer added successfully.');
}

public function show($id)
{
    $customer = Customer::with('leads', 'contacts')->findOrFail($id);

    return view('customer-view', compact('customer'));
}

public function edit($id)
{
    $customer = Customer::findOrFail($id);

    $countries = config('countries');


    return view('customer-edit', compact(
        'customer',
        'countries'
    ));
}

public function update(Request $request, $id)
{
    $customer = Customer::findOrFail($id);


    $validated = $request->validate([

        'Company_Name' => 'required|string|max:255',

        'Company_Email' => 'nullable|email|max:255',

        'Country_Code' => 'required|string|max:10',

        'Company_No' => 'required|string|max:20',

        'Status' => 'required|string',

    ]);


    $customer->update($validated);


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

public function notes()
{
    return $this->hasMany(Note::class, 'Customer_ID', 'Company_ID')->latest('Created_At');
}

}