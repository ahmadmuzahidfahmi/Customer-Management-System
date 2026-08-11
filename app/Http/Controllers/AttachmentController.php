<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'Entity_Type' => 'required|in:Contacts,Company,Leads,Activity,Notes',
            'Entity_ID'   => 'required|integer',
            'file'        => ['required', 'array'],
            'file.*'      => ['file', 'max:10240'],
        ]);

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'];
        $folder = Attachment::FOLDER_MAP[$validated['Entity_Type']];

        $rejected = [];
        $failed = [];
        $partial = [];
        $uploaded = 0;

        foreach ($request->file('file') as $file) {
            if (! in_array(strtolower($file->getClientOriginalExtension()), $allowedExtensions)) {
                $rejected[] = $file->getClientOriginalName();
                continue;
            }

            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $baseName = pathinfo($originalName, PATHINFO_FILENAME);
            $safeBaseName = trim(preg_replace('/[\\/:*?"<>|]/', '-', $baseName)) ?: 'file';

            $storedName = $safeBaseName . '.' . $extension;
            $relativePath = $folder . '/' . $storedName;

            // Check for name collisions against local (the primary/source of truth for naming)
            $counter = 1;
            while (Storage::disk('network')->exists($relativePath)) {
                $storedName = $safeBaseName . ' (' . $counter . ').' . $extension;
                $relativePath = $folder . '/' . $storedName;
                $counter++;
            }

            $isOnLocal = false;
            $isOnDrive = false;

            try {
                Storage::disk('network')->putFileAs($folder, $file, $storedName);
                $isOnLocal = true;
            } catch (\Throwable $e) {
                Log::error('Local upload failed', ['file' => $originalName, 'error' => $e->getMessage()]);
            }

            try {
                Storage::disk('google')->putFileAs($folder, $file, $storedName);
                $isOnDrive = true;
            } catch (\Throwable $e) {
                Log::error('Google Drive backup upload failed', ['file' => $originalName, 'error' => $e->getMessage()]);
            }

            if (! $isOnLocal && ! $isOnDrive) {
                $failed[] = $originalName;
                continue;
            }

            Attachment::create([
                'Entity_Type'   => $validated['Entity_Type'],
                'Entity_ID'     => $validated['Entity_ID'],
                'Original_Name' => $originalName,
                'Stored_Name'   => $storedName,
                'File_Path'     => $relativePath,
                'File_Type'     => $file->getClientMimeType(),
                'File_Size'     => $file->getSize(),
                'Uploaded_By'   => Auth::id(),
                'Is_On_Local'   => $isOnLocal,
                'Is_On_Drive'   => $isOnDrive,
            ]);

            

            $uploaded++;
            if (! $isOnLocal || ! $isOnDrive) {
                $partial[] = $originalName;
            }
        }

        $messages = [];
        if ($uploaded) $messages[] = "$uploaded file(s) uploaded.";
        if ($rejected) $messages[] = 'Rejected (unsupported type): ' . implode(', ', $rejected);
        if ($partial) $messages[] = 'Uploaded but missing one copy (local or backup): ' . implode(', ', $partial);
        if ($failed) $messages[] = 'Failed completely: ' . implode(', ', $failed);

        $type = ($rejected || $failed || $partial) ? 'error' : 'success';

        return back()->with($type, implode(' ', $messages) ?: 'Nothing was uploaded.');
    }

    public function show($id)
    {
        $attachment = Attachment::findOrFail($id);
        $disk = null;

        try {
            if (Storage::disk('network')->exists($attachment->File_Path)) {
                $disk = 'network';
            } elseif (Storage::disk('google')->exists($attachment->File_Path)) {
                $disk = 'google';
            }
        } catch (\Throwable $e) {
            Log::error('Attachment lookup failed', ['attachment_id' => $id, 'error' => $e->getMessage()]);
        }

        if (! $disk) {
            abort(404, 'File no longer exists in local or backup storage.');
        }

        return Storage::disk($disk)->response(
            $attachment->File_Path,
            $attachment->Original_Name
        );
    }

    public function destroy(Request $request, $id)
    {
        $attachment = Attachment::findOrFail($id);
        $deleteBackup = $request->boolean('delete_backup');

        try {
            if (Storage::disk('network')->exists($attachment->File_Path)) {
                Storage::disk('network')->delete($attachment->File_Path);
            }
        } catch (\Throwable $e) {
            Log::error('Local delete failed', ['attachment_id' => $id, 'error' => $e->getMessage()]);
        }

        if ($deleteBackup) {
            try {
                if (Storage::disk('google')->exists($attachment->File_Path)) {
                    Storage::disk('google')->delete($attachment->File_Path);
                }
            } catch (\Throwable $e) {
                Log::error('Backup delete failed', ['attachment_id' => $id, 'error' => $e->getMessage()]);
            }
        }

        $attachment->delete();

        return back()->with(
            'success',
            $deleteBackup ? 'Attachment and backup deleted.' : 'Attachment deleted. Backup kept in Google Drive.'
        );
    }

    public function bulkDestroy(Request $request)
{
    $validated = $request->validate([
        'ids'   => 'required|array',
        'ids.*' => 'integer|exists:attachments,Attachment_ID',
    ]);

    $deleteBackup = $request->boolean('delete_backup');
    $attachments = Attachment::whereIn('Attachment_ID', $validated['ids'])->get();
    $count = 0;

    foreach ($attachments as $attachment) {
        try {
            if (Storage::disk('network')->exists($attachment->File_Path)) {
                Storage::disk('network')->delete($attachment->File_Path);
            }
        } catch (\Throwable $e) {
            Log::error('Bulk local delete failed', ['attachment_id' => $attachment->Attachment_ID, 'error' => $e->getMessage()]);
        }

        if ($deleteBackup) {
            try {
                if (Storage::disk('google')->exists($attachment->File_Path)) {
                    Storage::disk('google')->delete($attachment->File_Path);
                }
            } catch (\Throwable $e) {
                Log::error('Bulk backup delete failed', ['attachment_id' => $attachment->Attachment_ID, 'error' => $e->getMessage()]);
            }
        }

        $attachment->delete();
        $count++;
    }

    return back()->with(
        'success',
        "$count attachment(s) deleted" . ($deleteBackup ? ' (including backups).' : '. Backups kept in Google Drive.')
    );
}

public function resync($id)
    {
        $attachment = Attachment::findOrFail($id);
        $fixed = [];
        $stillFailing = [];

        if (! $attachment->Is_On_Local) {
            try {
                if (Storage::disk('google')->exists($attachment->File_Path)) {
                    $contents = Storage::disk('google')->get($attachment->File_Path);
                    Storage::disk('network')->put($attachment->File_Path, $contents);
                    $attachment->Is_On_Local = true;
                    $fixed[] = 'local copy';
                } else {
                    $stillFailing[] = 'local copy (source missing on Drive too)';
                }
            } catch (\Throwable $e) {
                Log::error('Resync to local failed', ['attachment_id' => $id, 'error' => $e->getMessage()]);
                $stillFailing[] = 'local copy';
            }
        }

        if (! $attachment->Is_On_Drive) {
            try {
                if (Storage::disk('network')->exists($attachment->File_Path)) {
                    $contents = Storage::disk('network')->get($attachment->File_Path);
                    Storage::disk('google')->put($attachment->File_Path, $contents);
                    $attachment->Is_On_Drive = true;
                    $fixed[] = 'Drive backup';
                } else {
                    $stillFailing[] = 'Drive backup (source missing locally too)';
                }
            } catch (\Throwable $e) {
                Log::error('Resync to Drive failed', ['attachment_id' => $id, 'error' => $e->getMessage()]);
                $stillFailing[] = 'Drive backup';
            }
        }

        $attachment->save();

        if ($fixed && ! $stillFailing) {
            return back()->with('success', 'Restored: ' . implode(', ', $fixed) . '.');
        }
        if ($fixed && $stillFailing) {
            return back()->with('error', 'Restored: ' . implode(', ', $fixed) . '. Still missing: ' . implode(', ', $stillFailing) . '.');
        }
        if ($stillFailing) {
            return back()->with('error', 'Could not restore: ' . implode(', ', $stillFailing) . '.');
        }

        return back()->with('success', 'Already fully synced.');
    }

    public function syncStatus()
    {
        $attachments = Attachment::with('entity')
            ->where('Is_On_Local', false)
            ->orWhere('Is_On_Drive', false)
            ->orderBy('Created_At', 'desc')
            ->get();

        return view('attachments-sync-status', compact('attachments'));
    }

    public function verifyAll()
    {
        $attachments = Attachment::all();
        $updated = 0;

        foreach ($attachments as $attachment) {
            $onLocal = false;
            $onDrive = false;

            try {
                $onLocal = Storage::disk('network')->exists($attachment->File_Path);
            } catch (\Throwable $e) {
                Log::error('Verify local check failed', ['attachment_id' => $attachment->Attachment_ID, 'error' => $e->getMessage()]);
            }

            try {
                $onDrive = Storage::disk('google')->exists($attachment->File_Path);
            } catch (\Throwable $e) {
                Log::error('Verify drive check failed', ['attachment_id' => $attachment->Attachment_ID, 'error' => $e->getMessage()]);
            }

            if ($attachment->Is_On_Local !== $onLocal || $attachment->Is_On_Drive !== $onDrive) {
                $attachment->Is_On_Local = $onLocal;
                $attachment->Is_On_Drive = $onDrive;
                $attachment->save();
                $updated++;
            }
        }

        return back()->with('success', "Verification complete. $updated record(s) updated.");
    }
}