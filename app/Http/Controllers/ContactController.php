<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Note;
use App\Models\Attachment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;



class ContactController extends Controller
{
public function index(Request $request)
{
    $query = Contact::with('company');

    // Search
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('Contact_Name', 'like', "%{$search}%")
              ->orWhere('Contact_Email', 'like', "%{$search}%")
              ->orWhere('Contact_Role', 'like', "%{$search}%")
              ->orWhere('Contact_No', 'like', "%{$search}%");
        });
    }

    // Company filter
    if ($request->filled('company')) {
        $query->where('Company_ID', $request->company);
    }

    $contacts = $query->paginate(10)->withQueryString();

    $roles = Contact::whereNotNull('Contact_Role')
        ->where('Contact_Role', '!=', '')
        ->distinct()
        ->orderBy('Contact_Role')
        ->pluck('Contact_Role');

    // Stats Cards
    $contactsCount = Contact::count();

    $withEmail = Contact::whereNotNull('Contact_Email')
        ->where('Contact_Email', '!=', '')
        ->count();

    $withoutEmail = Contact::where(function ($q) {
        $q->whereNull('Contact_Email')
          ->orWhere('Contact_Email', '');
    })->count();

    $companies = Customer::orderBy('Company_Name')->get();

    return view('contacts', compact(
        'contacts',
        'contactsCount',
        'withEmail',
        'withoutEmail',
        'companies',
        'roles'
    ));
}

public function show($id)
{
    $contact = Contact::findOrFail($id);

    return view('contact-view', compact('contact'));
}

public function create()
{
    $customers = Customer::orderBy('Company_Name')->get();

    $countries = config('countries');

    return view('contact-create', compact(
        'customers',
        'countries'
    ));
}

public function store(Request $request)
{
    $validated = $request->validate([
        'Contact_Name'   => 'required|string|max:255',
        'Contact_Email'  => 'nullable|email|max:255',
        'Country_Code'   => 'required|string|max:10',
        'Contact_No'     => 'required|string|max:20',
        'Contact_Role'   => 'nullable|string|max:255',
        'Contact_Note'   => 'nullable|string',
        'Company_ID'     => 'required|exists:company,Company_ID',
        'Notes'          => 'nullable|array',
        'Notes.*.Subject' => 'nullable|string|max:255',
        'Notes.*.Content'  => 'nullable|string',
        'Attachments'    => 'nullable|array',
        'Attachments.*'  => 'file|max:10240',
    ]);

    $contact = Contact::create([
        'Contact_Name'  => $validated['Contact_Name'],
        'Contact_Email' => $validated['Contact_Email'] ?? null,
        'Country_Code'  => $validated['Country_Code'],
        'Contact_No'    => $validated['Contact_No'],
        'Contact_Role'  => $validated['Contact_Role'] ?? null,
        'Contact_Note'  => $validated['Contact_Note'] ?? null,
        'Company_ID'    => $validated['Company_ID'],
    ]);

    // Notes
    foreach ($request->input('Notes', []) as $note) {
        if (! empty($note['Content'])) {
            Note::create([
                'Subject'    => $note['Subject'] ?? null,
                'Content'    => $note['Content'],
                'Contact_ID' => $contact->Contact_ID,
            ]);
        }
    }

    // Attachments
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'];
    $folder = Attachment::FOLDER_MAP['Contacts'];
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
            while (Storage::disk('google')->exists($relativePath)) {
                $storedName = $safeBaseName . ' (' . $counter . ').' . $extension;
                $relativePath = $folder . '/' . $storedName;
                $counter++;
            }

            Storage::disk('google')->putFileAs($folder, $file, $storedName);

            Attachment::create([
                'Entity_Type'   => 'Contacts',
                'Entity_ID'     => $contact->Contact_ID,
                'Original_Name' => $originalName,
                'Stored_Name'   => $storedName,
                'File_Path'     => $relativePath,
                'File_Type'     => $file->getClientMimeType(),
                'File_Size'     => $file->getSize(),
                'Uploaded_By'   => Auth::id(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Google Drive upload failed on contact create', [
                'file' => $originalName,
                'error' => $e->getMessage(),
            ]);
            $failed[] = $originalName;
        }
    }

    $message = 'Contact created successfully.';
    if ($failed) {
        $message .= ' Some attachments failed to upload: ' . implode(', ', $failed);
    }

    return redirect()
        ->route('contacts')
        ->with($failed ? 'error' : 'success', $message);
}

public function edit($id)
{
    $contact = Contact::findOrFail($id);

    $customers = Customer::orderBy('Company_Name')->get();

$countries = config('countries');

return view('contact-edit', compact(
    'contact',
    'customers',
    'countries'
));}

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
    $contact = Contact::onlyTrashed()
        ->where('Contact_ID', $id)
        ->firstOrFail();

    $contact->forceDelete();

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