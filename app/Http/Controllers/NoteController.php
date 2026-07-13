<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;

class NoteController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'Subject' => 'nullable|string|max:255',
            'Content' => 'required|string',
            'Company_ID' => 'nullable|exists:company,Company_ID',
            'Contact_ID' => 'nullable|exists:contacts,Contact_ID',
            'Lead_ID' => 'nullable|exists:leads,Lead_ID',
        ]);

        Note::create($request->only([
            'Subject', 'Content', 'Company_ID', 'Contact_ID', 'Lead_ID',
        ]));

        return back()->with('success', 'Note added.');
    }

    public function destroy($id)
    {
        Note::findOrFail($id)->delete();

        return back()->with('success', 'Note deleted.');
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'Subject' => 'nullable|string|max:255',
        'Content' => 'required|string',
    ]);

    $note = Note::findOrFail($id);

    $note->update($request->only(['Subject', 'Content']));

    return back()->with('success', 'Note updated.');
}
}