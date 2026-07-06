<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Customer;


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
    return view('create');
}
public function store(Request $request)
{
    // Save customer here

    return redirect()->route('customers');
}

public function show($id)
{
    $customer = Customer::with('leads')->findOrFail($id);

    return view('customer-view', compact('customer'));
}

}