<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\CrmMessage;
use App\Models\Activity;
use App\Models\Contact;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Throwable;

class EmailController extends Controller
{
    /**
     * Send an email via Resend and log it as a completed Activity.
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'Contact_ID' => 'nullable|exists:contacts,Contact_ID',
            'Company_ID' => 'nullable|exists:company,Company_ID',
            'Lead_ID'    => 'nullable|exists:leads,Lead_ID',
            'Subject'    => 'required|string|max:255',
            'Body'       => 'required|string',
        ]);

        if (empty($validated['Contact_ID']) && empty($validated['Company_ID'])) {
            return response()->json([
                'success' => false,
                'message' => 'Must be linked to a Contact or Customer.',
            ], 422);
        }

        if (! empty($validated['Contact_ID'])) {
            $contact = Contact::find($validated['Contact_ID']);
            $recipientEmail = $contact->Contact_Email ?? null;
            $recipientName = $contact->Contact_Name ?? null;
        } else {
            $customer = Customer::find($validated['Company_ID']);
            $recipientEmail = $customer->Company_Email ?? null;
            $recipientName = $customer->Company_Name ?? null;
        }

        if (! $recipientEmail) {
            return response()->json([
                'success' => false,
                'message' => 'This recipient has no email address on file.',
            ], 422);
        }

        try {
            Mail::to($recipientEmail)->send(new CrmMessage(
                $validated['Subject'],
                $validated['Body'],
                Auth::user()->User_Name,
            ));
        } catch (TransportExceptionInterface|Throwable $e) {
            Log::error('Resend email send failed', [
                'to'      => $recipientEmail,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'The email could not be sent. Please check your Resend configuration.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 502);
        }

        $activity = Activity::create([
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

        return response()->json([
            'success' => true,
            'message' => "Email sent to {$recipientName}.",
            'data'    => [
                'recipient'   => $recipientEmail,
                'activity_id' => $activity->Activity_ID ?? $activity->id ?? null,
            ],
        ], 200);
    }
}
