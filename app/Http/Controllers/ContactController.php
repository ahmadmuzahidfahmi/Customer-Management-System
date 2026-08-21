<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\Note;
use App\Models\Attachment;
use App\Models\Leads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Pin;


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
              ->orWhere('Contact_No', 'like', "%{$search}%")
              ->orWhereHas('company', function ($q2) use ($search) {
                  $q2->where('Company_Name', 'like', "%{$search}%");
              });
        });
    }

    // Company filter
    if ($request->filled('company')) {
        $query->where('Company_ID', $request->company);
    }

switch ($request->get('sort', 'newest')) {
        case 'oldest':
            $query->orderBy('Created_At', 'asc');
            break;
        case 'name_asc':
            $query->orderBy('Contact_Name', 'asc');
            break;
        case 'name_desc':
            $query->orderBy('Contact_Name', 'desc');
            break;
        case 'company_asc':
            $query->join('company', 'contacts.Company_ID', '=', 'company.Company_ID')
                  ->orderBy('company.Company_Name', 'asc')
                  ->orderBy('contacts.Contact_Name', 'asc')
                  ->select('contacts.*');
            break;
        case 'company_desc':
            $query->join('company', 'contacts.Company_ID', '=', 'company.Company_ID')
                  ->orderBy('company.Company_Name', 'desc')
                  ->orderBy('contacts.Contact_Name', 'asc')
                  ->select('contacts.*');
            break;
        default:
            $query->orderBy('Created_At', 'desc');
            break;
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

$pinnedContactIds = Pin::where('User_ID', Auth::id())
        ->where('Entity_Type', 'Contact')
        ->pluck('Entity_ID');

    $pinnedContacts = Contact::whereIn('Contact_ID', $pinnedContactIds)
        ->with('company')
        ->orderBy('Contact_Name')
        ->get();

    $companies = Customer::orderBy('Company_Name')->get();

    return view('contacts', compact(
        'contacts',
        'contactsCount',
        'withEmail',
        'withoutEmail',
        'companies',
        'roles',
        'pinnedContactIds',
        'pinnedContacts'
    ));
}

public function show($id)
{
    $contact = Contact::with('company', 'leads')->findOrFail($id);

    $allOtherLeads = \App\Models\Leads::with('company')
        ->where('Contact_ID', '!=', $contact->Contact_ID)
        ->orWhereNull('Contact_ID')
        ->orderBy('Lead_Name')
        ->get()
        ->map(fn ($l) => [
            'id'      => $l->Lead_ID,
            'name'    => $l->Lead_Name,
            'company' => $l->company->Company_Name ?? null,
        ]);

    return view('contact-view', compact('contact', 'allOtherLeads'));
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
        'Country_Code'   => ['required', 'regex:/^\+[0-9]{1,4}$/'],
        'Contact_No'     => 'required|digits_between:5,15',
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

    $validated = $request->validate([
        'Company_ID' => 'required|exists:company,Company_ID',
        'Country_Code' => ['required', 'regex:/^\+[0-9]{1,4}$/'],
        'Contact_No' => 'required|digits_between:5,15',
    ]);

    $contact->update([
        'Contact_Name' => $request->Contact_Name,
        'Contact_Email' => $request->Contact_Email,
        'Contact_No' => $validated['Contact_No'],
        'Company_ID' => $validated['Company_ID'],
        'Contact_Role' => $request->Contact_Role,
        'Contact_Note' => $request->Contact_Note,
        'Country_Code' => $validated['Country_Code'],
    ]);

    return redirect()
        ->route('contacts.show', $contact->Contact_ID)
        ->with('success', 'Contact updated successfully.');
} 

public function togglePin($id)
{
    $contact = Contact::findOrFail($id);

    $pin = Pin::where('User_ID', Auth::id())
        ->where('Entity_Type', 'Contact')
        ->where('Entity_ID', $contact->Contact_ID)
        ->first();

    if ($pin) {
        $pin->delete();
    } else {
        Pin::create([
            'User_ID'     => Auth::id(),
            'Entity_Type' => 'Contact',
            'Entity_ID'   => $contact->Contact_ID,
        ]);
    }

    return back();
}

public function addLead(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);

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
            'Contact_ID'      => $contact->Contact_ID,
            'Company_ID'      => $contact->Company_ID,
        ]);

        return back()->with('success', 'Lead added.');
    }

    public function linkLead(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);

        $validated = $request->validate([
            'Lead_ID' => 'required|exists:leads,Lead_ID',
        ]);

        Leads::where('Lead_ID', $validated['Lead_ID'])->update([
            'Contact_ID' => $contact->Contact_ID,
            'Company_ID' => $contact->Company_ID,
        ]);

        return back()->with('success', 'Lead linked to this contact.');
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