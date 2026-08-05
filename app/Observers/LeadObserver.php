<?php

namespace App\Observers;

use App\Models\Leads;
use App\Models\Note;
use App\Models\Activity;
use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; 

class LeadObserver
{
    public function forceDeleting(Leads $lead): void
    {
        $noteIds = Note::where('Lead_ID', $lead->Lead_ID)->pluck('Note_ID');
        $activityIds = Activity::where('Lead_ID', $lead->Lead_ID)->pluck('Activity_ID');

        $attachments = Attachment::where(function ($q) use ($lead, $noteIds, $activityIds) {
            $q->where(fn ($q2) => $q2->where('Entity_Type', 'Leads')->where('Entity_ID', $lead->Lead_ID))
              ->orWhere(fn ($q2) => $q2->where('Entity_Type', 'Notes')->whereIn('Entity_ID', $noteIds))
              ->orWhere(fn ($q2) => $q2->where('Entity_Type', 'Activity')->whereIn('Entity_ID', $activityIds));
        })->get();

foreach ($attachments as $attachment) {
    try {
        if (Storage::disk('google')->exists($attachment->File_Path)) {
            Storage::disk('google')->delete($attachment->File_Path);
        }
        $attachment->delete();
    } catch (\Throwable $e) {
        Log::error('Google Drive cleanup failed during force-delete', [
            'attachment_id' => $attachment->Attachment_ID,
            'path' => $attachment->File_Path,
            'error' => $e->getMessage(),
        ]);
        }
    }
}}