<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'Entity_Type' => 'required|in:Contacts,Company,Leads,Activity,Notes',
            'Entity_ID'   => 'required|integer',
            'file'        => 'required|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt',
        ]);

        $file = $request->file('file');
        $folder = Attachment::FOLDER_MAP[$validated['Entity_Type']];
        $storedName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $relativePath = $folder . '/' . $storedName;

        Storage::disk('network')->putFileAs($folder, $file, $storedName);

        Attachment::create([
            'Entity_Type'   => $validated['Entity_Type'],
            'Entity_ID'     => $validated['Entity_ID'],
            'Original_Name' => $file->getClientOriginalName(),
            'Stored_Name'   => $storedName,
            'File_Path'     => $relativePath,
            'File_Type'     => $file->getClientMimeType(),
            'File_Size'     => $file->getSize(),
            'Uploaded_By'   => Auth::id(),
        ]);

        return back()->with('success', 'File uploaded.');
    }

    public function show($id)
    {
        $attachment = Attachment::findOrFail($id);

        if (! Storage::disk('network')->exists($attachment->File_Path)) {
            abort(404, 'File no longer exists on the share.');
        }

        return Storage::disk('network')->response(
            $attachment->File_Path,
            $attachment->Original_Name
        );
    }

    public function destroy($id)
    {
        $attachment = Attachment::findOrFail($id);

        if (Storage::disk('network')->exists($attachment->File_Path)) {
            Storage::disk('network')->delete($attachment->File_Path);
        }

        $attachment->delete();

        return back()->with('success', 'Attachment deleted.');
    }
}