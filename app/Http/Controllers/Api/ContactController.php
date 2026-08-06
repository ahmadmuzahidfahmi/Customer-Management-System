<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::latest('Contact_ID')->paginate(15);

        $pinnedContacts = Contact::with('company')
         ->where('Is_Pinned', 1)
        ->orderBy('Contact_Name')
        ->get();

        return ContactResource::collection($contacts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Contact_Name'  => 'required|string|max:255',
            'Contact_Email' => 'nullable|email|max:255',
            'Contact_No'    => 'nullable|string|max:20',
            'Country_Code'  => 'nullable|string|max:10',
            'Contact_Role'  => 'nullable|string|max:255',
            'Contact_Note'  => 'nullable|string',
            'Company_ID'    => 'required|exists:company,Company_ID',
        ]);

        $contact = Contact::create($validated);

        return response()->json([
            'success' => true,
            'data'    => new ContactResource($contact),
            'message' => 'Contact created successfully',
        ], 201);
    }

    public function show($id)
    {
        $contact = Contact::find($id);

        if (! $contact) {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found',
            ], 404);
        }

        return new ContactResource($contact);
    }

    public function update(Request $request, $id)
    {
        $contact = Contact::find($id);

        if (! $contact) {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found',
            ], 404);
        }

        $validated = $request->validate([
            'Contact_Name'  => 'sometimes|required|string|max:255',
            'Contact_Email' => 'nullable|email|max:255',
            'Contact_No'    => 'nullable|string|max:20',
            'Country_Code'  => 'nullable|string|max:10',
            'Contact_Role'  => 'nullable|string|max:255',
            'Contact_Note'  => 'nullable|string',
            'Company_ID'    => 'sometimes|required|exists:company,Company_ID',
        ]);

        $contact->update($validated);

        return response()->json([
            'success' => true,
            'data'    => new ContactResource($contact),
            'message' => 'Contact updated successfully',
        ]);
    }

    public function destroy($id)
    {
        $contact = Contact::find($id);

        if (! $contact) {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found',
            ], 404);
        }

        $contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact deleted successfully',
        ]);
    }
}