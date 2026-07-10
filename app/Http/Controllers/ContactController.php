<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Customer;




class ContactController extends Controller
{
    public function index(Request $request)
{
    $query = contact::query();

    // Header search bar — searches across multiple fields at once
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('Contact_Name', 'like', '%' . $search . '%')
              ->orWhere('Contact_Email', 'like', '%' . $search . '%')
              ->orWhere('Contact_Role', 'like', '%' . $search . '%')
              ->orWhere('Contact_No', 'like', '%' . $search . '%');
        });
    }

    // Reserved for the future filter panel (applied on top of search, not instead of it)
    if ($request->filled('contact')) {
        $query->where('Contact_Name', 'like', '%' . $request->company . '%');
    }

    if ($request->filled('status')) {
        $query->where('Status', $request->status);
    }

    $contacts = $query->paginate(10);

    return view('contacts', compact('contacts'));
}

public function show($id)
{
    $contact = Contact::findOrFail($id);

    return view('contact-view', compact('contact'));
}

public function create()
{
    $customers = Customer::orderBy('Company_Name')->get();

    return view('contact-create', compact('customers'));
}

public function store(Request $request)
{

    Contact::create([
        'Contact_Name' => $request->Contact_Name,
        'Contact_Email' => $request->Contact_Email,
        'Contact_No' => $request->Contact_No,
        'Company_ID' => $request->Company_ID,
        'Contact_Role' => $request->Contact_Role,
        'Contact_Note' => $request->Contact_Note,
        'Country_Code' => $request->Country_Code,
    ]);

    return redirect()
        ->route('contacts')
        ->with('success', 'Contact created successfully.');
}

public function edit($id)
{
    $contact = Contact::findOrFail($id);

    $customers = Customer::orderBy('Company_Name')->get();

    return view('contact-edit', compact('contact', 'customers'));
}

public function update(Request $request, $id)
{
    $contact = Contact::findOrFail($id);

    $contact->update([
        'Contact_Name' => $request->Contact_Name,
        'Contact_Email' => $request->Contact_Email,
        'Contact_No' => $request->Contact_No,
        'Company_ID' => $request->Company_ID,
        'Contact_Role' => $request->Contact_Role,
        'Contact_Note' => $request->Contact_Note,
        'Country_Code' => $request->Country_Code,
    ]);

    return redirect()
        ->route('contacts.show', $contact->Contact_ID)
        ->with('success', 'Contact updated successfully.');
} 

public function destroy($id)
{
    $contact = Contact::findOrFail($id);

    $contact->delete();

    return redirect()
        ->route('contacts')
        ->with('success', 'Contact moved to recycle bin.');
}
public function recycleBin()
{
    $contacts = Contact::onlyTrashed()->get();

    return view('recycle-bin', compact('contacts'));
}
public function restore($id)
{
    Contact::onlyTrashed()
        ->findOrFail($id)
        ->restore();

    return back();
}

public function forceDelete($id)
{
    Contact::onlyTrashed()
        ->where('Contact_ID', $id)
        ->forceDelete();

    return back();
}

public function showDeleted($id)
{
    $contact = Contact::onlyTrashed()
        ->where('Contact_ID', $id)
        ->firstOrFail();

    return view('contact-view', compact('contact'));
}
}