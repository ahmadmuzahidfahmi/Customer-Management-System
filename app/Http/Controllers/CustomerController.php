<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Customer;
use App\Models\Contact;
use App\Models\Leads;
use App\Models\Note;
use App\Models\Attachment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Pin;


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

    $pinnedCustomerIds = Pin::where('User_ID', Auth::id())
        ->where('Entity_Type', 'Company')
        ->pluck('Entity_ID');

    $pinnedCustomers = Customer::whereIn('Company_ID', $pinnedCustomerIds)
        ->orderBy('Company_Name')
        ->get();

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
        'inactiveCustomers',
        'pinnedCustomers',
        'pinnedCustomerIds'

    ));
}

public function create()
{
    $countries = config('countries');

    $existingContacts = Contact::with('company')->orderBy('Contact_Name')->get()->map(fn ($c) => [
        'id'      => $c->Contact_ID,
        'name'    => $c->Contact_Name,
        'company' => $c->company->Company_Name ?? null,
    ]);

    $existingLeads = Leads::with('company')->orderBy('Lead_Name')->get()->map(fn ($l) => [
        'id'      => $l->Lead_ID,
        'name'    => $l->Lead_Name,
        'company' => $l->company->Company_Name ?? null,
    ]);

    return view('customer-create', compact('countries', 'existingContacts', 'existingLeads'));
}

public function store(StoreCustomerRequest $request)
{
    $validated = $request->validated();

    $customer = Customer::create([
        'Company_Name'  => $validated['Company_Name'],
        'Company_Email' => $validated['Company_Email'] ?? null,
        'Country_Code'  => $validated['Country_Code'],
        'Company_No'    => $validated['Company_No'],
        'Status'        => $validated['Status'],
    ]);

    // Notes
    foreach ($request->input('Notes', []) as $note) {
        if (! empty($note['Content'])) {
            Note::create([
                'Subject'    => $note['Subject'] ?? null,
                'Content'    => $note['Content'],
                'Company_ID' => $customer->Company_ID,
            ]);
        }
    }
    

    // Attachments
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'];
    $folder = Attachment::FOLDER_MAP['Company'];
    $failed = [];

    foreach ($request->file('Attachments', []) as $file) {
        if (! $file || ! in_array(strtolower($file->getClientOriginalExtension()), $allowedExtensions)) {
            continue;
        }

        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $safeBaseName = trim(preg_replace('/[\\/:*?"<>|]/', '-', $baseName)) ?: 'file';

        $storedName = $safeBaseName . '.' . $extension;
        $relativePath = $folder . '/' . $storedName;

        try {
            $counter = 1;
$isOnLocal = false;
        $isOnDrive = false;

        try {
            Storage::disk('network')->putFileAs($folder, $file, $storedName);
            $isOnLocal = true;
        } catch (\Throwable $e) {
            Log::error('Local upload failed on customer create', ['file' => $originalName, 'error' => $e->getMessage()]);
        }

        try {
            Storage::disk('google')->putFileAs($folder, $file, $storedName);
            $isOnDrive = true;
        } catch (\Throwable $e) {
            Log::error('Google Drive backup upload failed on customer create', ['file' => $originalName, 'error' => $e->getMessage()]);
        }

        if (! $isOnLocal && ! $isOnDrive) {
            $failed[] = $originalName;
            continue;
        }

        Attachment::create([
            'Entity_Type'   => 'Company',
            'Entity_ID'     => $customer->Company_ID,
            'Original_Name' => $originalName,
            'Stored_Name'   => $storedName,
            'File_Path'     => $relativePath,
            'File_Type'     => $file->getClientMimeType(),
            'File_Size'     => $file->getSize(),
            'Uploaded_By'   => Auth::id(),
            'Is_On_Local'   => $isOnLocal,
            'Is_On_Drive'   => $isOnDrive,
        ]);
        } catch (\Throwable $e) {
            Log::error('Google Drive upload failed on customer create', [
                'file' => $originalName,
                'error' => $e->getMessage(),
            ]);
            $failed[] = $originalName;
        }
    }
            // Contacts
    foreach ($request->input('Contacts', []) as $contactData) {
        if (! empty($contactData['Contact_Name'])) {
            \App\Models\Contact::create([
                'Contact_Name'  => $contactData['Contact_Name'],
                'Contact_Email' => $contactData['Contact_Email'] ?? null,
                'Country_Code'  => $contactData['Country_Code'] ?? '+60',
                'Contact_No'    => $contactData['Contact_No'] ?? null,
                'Contact_Role'  => $contactData['Contact_Role'] ?? null,
                'Company_ID'    => $customer->Company_ID,
            ]);
        }
    }

    // Leads
    foreach ($request->input('Leads', []) as $leadData) {
        if (! empty($leadData['Lead_Name'])) {
            \App\Models\Leads::create([
                'Lead_Name'       => $leadData['Lead_Name'],
                'Source'          => $leadData['Source'] ?? null,
                'Status'          => $leadData['Status'] ?? 'New',
                'Estimated_Value' => $leadData['Estimated_Value'] ?? null,
                'Company_ID'      => $customer->Company_ID,
            ]);
        }
    }

        // Link existing contacts (reassign them to this new company)
    if ($request->filled('Existing_Contacts')) {
        Contact::whereIn('Contact_ID', $request->input('Existing_Contacts'))
            ->update(['Company_ID' => $customer->Company_ID]);
    }

    // Link existing leads (reassign them to this new company)
    if ($request->filled('Existing_Leads')) {
        Leads::whereIn('Lead_ID', $request->input('Existing_Leads'))
            ->update(['Company_ID' => $customer->Company_ID]);
    }

    $message = 'Customer added successfully.';
    if ($failed) {
        $message .= ' Some attachments failed to upload: ' . implode(', ', $failed);
    }

    return redirect()
        ->route('customers')
        ->with($failed ? 'error' : 'success', $message);
    }

public function show($id)
{
    $customer = Customer::with('leads', 'contacts')->findOrFail($id);

    $contactIds = $customer->contacts()->pluck('Contact_ID');
    $leadIds = $customer->leads()->pluck('Lead_ID');

    $allAttachments = Attachment::with('entity')
        ->where(function ($q) use ($customer, $contactIds, $leadIds) {
            $q->where(fn ($q2) => $q2->where('Entity_Type', 'Company')->where('Entity_ID', $customer->Company_ID))
              ->orWhere(fn ($q2) => $q2->where('Entity_Type', 'Contacts')->whereIn('Entity_ID', $contactIds))
              ->orWhere(fn ($q2) => $q2->where('Entity_Type', 'Leads')->whereIn('Entity_ID', $leadIds));
        })
        ->latest('Created_At')
        ->get();

    $allOtherContacts = Contact::with('company')
        ->where('Company_ID', '!=', $customer->Company_ID)
        ->orderBy('Contact_Name')
        ->get()
        ->map(fn ($c) => [
            'id'      => $c->Contact_ID,
            'name'    => $c->Contact_Name,
            'company' => $c->company->Company_Name ?? null,
        ]);

    $allOtherLeads = Leads::with('company')
        ->where('Company_ID', '!=', $customer->Company_ID)
        ->orderBy('Lead_Name')
        ->get()
        ->map(fn ($l) => [
            'id'      => $l->Lead_ID,
            'name'    => $l->Lead_Name,
            'company' => $l->company->Company_Name ?? null,
        ]);

    return view('customer-view', compact('customer', 'allAttachments', 'allOtherContacts', 'allOtherLeads'));
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

public function update(UpdateCustomerRequest $request, $id)
{
    $customer = Customer::findOrFail($id);

    $customer->update($request->validated());


    return redirect()
        ->route('customers.show', $customer->Company_ID)
        ->with('success', 'Customer updated successfully.');
}

public function togglePin($id)
{
    $customer = Customer::findOrFail($id);

    $pin = Pin::where('User_ID', Auth::id())
        ->where('Entity_Type', 'Company')
        ->where('Entity_ID', $customer->Company_ID)
        ->first();

    if ($pin) {
        $pin->delete();
    } else {
        Pin::create([
            'User_ID'     => Auth::id(),
            'Entity_Type' => 'Company',
            'Entity_ID'   => $customer->Company_ID,
        ]);
    }

    return back();
}

public function addContact(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'Contact_Name'  => 'required|string|max:255',
            'Contact_Email' => 'nullable|email|max:255',
            'Country_Code'  => 'nullable|string|max:10',
            'Contact_No'    => 'nullable|string|max:20',
            'Contact_Role'  => 'nullable|string|max:255',
        ]);

        Contact::create([
            'Contact_Name'  => $validated['Contact_Name'],
            'Contact_Email' => $validated['Contact_Email'] ?? null,
            'Country_Code'  => $validated['Country_Code'] ?? '+60',
            'Contact_No'    => $validated['Contact_No'] ?? null,
            'Contact_Role'  => $validated['Contact_Role'] ?? null,
            'Company_ID'    => $customer->Company_ID,
        ]);

        return back()->with('success', 'Contact added.');
    }

    public function linkContact(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'Contact_ID' => 'required|exists:contacts,Contact_ID',
        ]);

        Contact::where('Contact_ID', $validated['Contact_ID'])
            ->update(['Company_ID' => $customer->Company_ID]);

        return back()->with('success', 'Contact linked to this customer.');
    }

    public function addLead(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'Lead_Name'       => 'required|string|max:255',
            'Source'          => 'nullable|string|max:255',
            'Status'          => 'nullable|string',
            'Estimated_Value' => 'nullable|numeric',
        ]);

        Leads::create([
            'Lead_Name'       => $validated['Lead_Name'],
            'Source'          => $validated['Source'] ?? null,
            'Status'          => $validated['Status'] ?? 'New',
            'Estimated_Value' => $validated['Estimated_Value'] ?? null,
            'Company_ID'      => $customer->Company_ID,
        ]);

        return back()->with('success', 'Lead added.');
    }

    public function linkLead(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'Lead_ID' => 'required|exists:leads,Lead_ID',
        ]);

        Leads::where('Lead_ID', $validated['Lead_ID'])
            ->update(['Company_ID' => $customer->Company_ID]);

        return back()->with('success', 'Lead linked to this customer.');
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
    $customer = Customer::onlyTrashed()
        ->where('Company_ID', $id)
        ->firstOrFail();

    $customer->forceDelete();

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