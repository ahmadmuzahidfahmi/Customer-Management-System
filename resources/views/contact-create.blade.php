@extends('layouts.app')

@section('content')
<head> 
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />


</head>

<div class="flex items-center gap-2 mb-6">
    <h1 class="text-3xl font-bold text-gray-800">
        Contacts
    </h1>

    <span class="text-lg text-gray-500">
        / Add Contact
    </span>
</div>

<div class="bg-white rounded-lg shadow p-6">

<form action="{{ route('contacts.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Contact Name -->
        <div>
            <label class="block text-sm font-medium mb-1">
                Contact Name
            </label>

            <input
                type="text"
                name="Contact_Name"
                required
                class="w-full border rounded-lg px-3 py-2">
        </div>

        <!-- Email -->
        <div>
            <label class="block text-sm font-medium mb-1">
                Email
            </label>

            <input
                type="email"
                name="Contact_Email"
                class="w-full border rounded-lg px-3 py-2">
        </div>

<!-- Phone Number -->
<div>
    <label class="block text-sm font-medium mb-1">
        Phone Number
    </label>

    @include('partials.phone-input', [
        'countryField' => 'Country_Code',
        'numberField' => 'Contact_No',
        'countries' => $countries,
        'selectedCode' => old('Country_Code', '+60'),
        'selectedNumber' => old('Contact_No'),
    ])

</div>

        <!-- Position -->
        <div>
            <label class="block text-sm font-medium mb-1">
                Position
            </label>

            <input
                type="text"
                name="Contact_Role"
                placeholder="Manager, CEO, Director..."
                class="w-full border rounded-lg px-3 py-2">
        </div>

        <!-- Company -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-1">
                Company
            </label>

            @include('partials.entity-picker', [
                'fieldName' => 'Company_ID',
                'options' => $customers->map(fn ($c) => ['id' => $c->Company_ID, 'label' => $c->Company_Name]),
                'placeholder' => 'Select Company',
                'title' => 'Select a Company',
                'selectedId' => old('Company_ID'),
            ])
        </div>

        <!-- Notes -->
        <div class="md:col-span-2" x-data="{ notes: [{ subject: '', content: '' }] }">
            <label class="block text-sm font-medium mb-1">
                Notes (Optional)
            </label>

            <template x-for="(note, index) in notes" :key="index">
                <div class="space-y-2 mb-3 border rounded-lg p-3 bg-gray-50">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-400" x-text="'Note ' + (index + 1)"></span>
                        <button
                            type="button"
                            x-show="notes.length > 1"
                            @click="notes.splice(index, 1)"
                            class="text-red-500 hover:text-red-700 text-xs">
                            Remove
                        </button>
                    </div>

                    <input
                        type="text"
                        :name="`Notes[${index}][Subject]`"
                        x-model="note.subject"
                        placeholder="Subject (optional)"
                        class="w-full border rounded-lg px-3 py-2 text-sm">

                    <textarea
                        :name="`Notes[${index}][Content]`"
                        x-model="note.content"
                        rows="3"
                        placeholder="Write a note..."
                        class="w-full border rounded-lg px-3 py-2 text-sm"></textarea>
                </div>
            </template>

            <button
                type="button"
                @click="notes.push({ subject: '', content: '' })"
                class="text-cyan-600 hover:text-cyan-800 text-sm font-medium">
                + Add another note
            </button>
        </div>

        <!-- Attachments -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-1">
                Attachments (Optional)
            </label>

            <input
                type="file"
                name="Attachments[]"
                multiple
                class="w-full border rounded-lg px-3 py-2 text-sm bg-white">

            <p class="text-xs text-gray-500 mt-1">Max 10MB each. Images, PDF, Word, Excel, or text files.</p>
        </div>

    </div>

    <div class="mt-6 flex gap-3">

        <button
            type="submit"
            class="px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700">

            Save Contact

        </button>

        <a href="{{ route('contacts') }}"
           class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">

            Back

        </a>

    </div>

</form>

</div>

@endsection