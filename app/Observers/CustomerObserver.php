<?php

namespace App\Observers;

use App\Models\Customer;
use App\Models\Activity;
use App\Models\Attachment;
use App\Models\Note;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; 

class CustomerObserver
{
    public function deleting(Customer $customer): void
    {
        // Soft delete related leads and contacts when the customer is soft deleted
        $customer->leads()->delete();
        $customer->contacts()->delete();
    }

    public function restoring(Customer $customer): void
    {
        // Restore related leads and contacts when the customer is restored
        $customer->leads()->onlyTrashed()->restore();
        $customer->contacts()->onlyTrashed()->restore();
    }

    public function forceDeleting(Customer $customer): void
    {
        $contactIds = $customer->contacts()->withTrashed()->pluck('Contact_ID');
        $leadIds    = $customer->leads()->withTrashed()->pluck('Lead_ID');
        $activityIds = Activity::where('Company_ID', $customer->Company_ID)->pluck('Activity_ID');
        $noteIds    = Note::where('Company_ID', $customer->Company_ID)->pluck('Note_ID');

        $attachments = Attachment::where(function ($q) use ($customer, $contactIds, $leadIds, $activityIds, $noteIds) {
            $q->where(fn ($q2) => $q2->where('Entity_Type', 'Company')->where('Entity_ID', $customer->Company_ID))
              ->orWhere(fn ($q2) => $q2->where('Entity_Type', 'Contacts')->whereIn('Entity_ID', $contactIds))
              ->orWhere(fn ($q2) => $q2->where('Entity_Type', 'Leads')->whereIn('Entity_ID', $leadIds))
              ->orWhere(fn ($q2) => $q2->where('Entity_Type', 'Activity')->whereIn('Entity_ID', $activityIds))
              ->orWhere(fn ($q2) => $q2->where('Entity_Type', 'Notes')->whereIn('Entity_ID', $noteIds));
        })->get();

    foreach ($attachments as $attachment) {
        try {
            if (Storage::disk('google')->exists($attachment->File_Path)) {
                Storage::disk('google')->delete($attachment->File_Path);
        }
        $attachment->delete();
    }   catch (\Throwable $e) {
            Log::error('Google Drive cleanup failed during force-delete', [
                'attachment_id' => $attachment->Attachment_ID,
                'path' => $attachment->File_Path,
                'error' => $e->getMessage(),
        ]);
        }
    }
}}