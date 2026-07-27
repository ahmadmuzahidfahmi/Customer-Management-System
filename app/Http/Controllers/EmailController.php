<?php

namespace App\Http\Controllers;

use App\Mail\CrmMessage;
use App\Models\Activity;
use App\Models\Contact;
use App\Models\Leads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'Contact_ID' => 'nullable|exists:contacts,Contact_ID',
            'Lead_ID'    => 'nullable|exists:leads,Lead_ID',
            'Subject'    => 'required|string|max:255',
            'Body'       => 'required|string',
        ]);

        if (empty($validated['Contact_ID']) && empty($validated['Lead_ID'])) {
            return back()->withErrors(['Contact_ID' => 'Must be linked to a Lead or a Contact.']);
        }

        $recipientEmail = null;
        $recipientName = null;

        if (! empty($validated['Contact_ID'])) {
            $contact = Contact::findOrFail($validated['Contact_ID']);
            $recipientEmail = $contact->Contact_Email;
            $recipientName = $contact->Contact_Name;
        } else {
            $lead = Leads::findOrFail($validated['Lead_ID']);
            $recipientEmail = $lead->contact->Contact_Email ?? null;
            $recipientName = $lead->contact->Contact_Name ?? $lead->Lead_Name;
        }

        if (! $recipientEmail) {
            return back()->withErrors(['Contact_ID' => 'This recipient has no email address on file.']);
        }

        Mail::to($recipientEmail)->send(new CrmMessage(
            $validated['Subject'],
            $validated['Body'],
            Auth::user()->User_Name,
        ));

        // Log the send as a completed Activity
        Activity::create([
            'Activity_Type'   => 'Email',
            'Subject'         => $validated['Subject'],
            'Activity_Detail' => $validated['Body'],
            'Status'          => 'Completed',
            'Completed_At'    => now(),
            'Contact_ID'      => $validated['Contact_ID'] ?? null,
            'Lead_ID'         => $validated['Lead_ID'] ?? null,
            'User_ID'         => Auth::id(),
            'Assigned_To'     => Auth::id(),
        ]);

        return back()->with('success', "Email sent to {$recipientName}.");
    }
}