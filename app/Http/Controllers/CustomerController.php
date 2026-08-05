<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Customer;
use App\Models\Contact;
use App\Models\Leads;
use App\Models\Note;
use App\Models\Attachment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


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
        'Company_Name'  => 'required|string|max:255',
        'Company_Email' => 'nullable|email|max:255',
        'Country_Code'  => 'required|string|max:10',
        'Company_No'    => 'required|string|max:20',
        'Status'        => 'required|string',
        'Notes'         => 'nullable|array',
        'Notes.*.Subject' => 'nullable|string|max:255',
        'Notes.*.Content'  => 'nullable|string',
        'Attachments'   => 'nullable|array',
        'Attachments.*' => 'file|max:10240',
    ]);

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