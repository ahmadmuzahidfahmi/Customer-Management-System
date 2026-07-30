<head> 
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

@extends('layouts.app')

@section('content')

<div x-data="{ emailing: false }" class="max-w-4xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex justify-between items-center">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                {{ $contact->Contact_Name }}
            </h1>

            <p class="text-gray-500">
                {{ $contact->Contact_Role ?? 'Contact' }}
            </p>
        </div>

    <div class="flex gap-2">

    <button
        type="button"
        @click="emailing = true"
        class="flex items-center justify-center w-36 h-12 bg-gray-700 text-white rounded-lg hover:bg-gray-800">
        Send Email
    </button>

    <a href="{{ route('contacts.edit', $contact->Contact_ID) }}"
       class="flex items-center justify-center w-36 h-12 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700">
        Edit Contact
    </a>

    <form action="{{ route('contacts.destroy', $contact->Contact_ID) }}"
          method="POST"
          onsubmit="return confirm('Move this contact to the Recycle Bin?');">

        @csrf
        @method('DELETE')

        <button
            type="submit"
            class="flex items-center justify-center w-36 h-12 bg-red-600 text-white rounded-lg hover:bg-red-700">
            Delete
        </button>
    </form>
</div>

    </div>

    <!-- Contact Information -->
    <div class="bg-white rounded-lg shadow p-6">

        <h2 class="text-lg font-semibold mb-4">
            Contact Information
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <p class="text-sm text-gray-500">Full Name</p>
                <p class="font-medium">
                    {{ $contact->Contact_Name }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Email</p>
                <p class="font-medium">
                    {{ $contact->Contact_Email ?? 'N/A' }}
                </p>
            </div>

<div>
    <p class="text-sm text-gray-500">Phone</p>

    @if($contact->Contact_No)

        <a
            href="https://wa.me/{{ $contact->whatsapp_number }}"
            target="_blank"
            class="font-medium text-green-600 hover:text-green-700 hover:underline">

            {{ $contact->Country_Code }} {{ $contact->Contact_No }}

        </a>

    @else

        <p class="font-medium">N/A</p>

    @endif
</div>

            <div>
                <p class="text-sm text-gray-500">Position</p>
                <p class="font-medium">
                    {{ $contact->Contact_Role ?? 'N/A' }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Note</p>
                <p class="font-medium">
                {{ $contact->Contact_Note ?? 'No notes available.' }}
                </p>
            </div>

        </div>

    </div>

    <!-- Note -->

    @include('partials.notes', [
    'notes' => $contact->notes()->latest('Created_At')->get(),
    'ownerField' => 'Contact_ID',
    'ownerId' => $contact->Contact_ID,
])
    <!-- Activities -->

    @include('partials.activities', [
        'activities' => $contact->activities,
        'ownerField' => 'Contact_ID',
        'ownerId' => $contact->Contact_ID,
    ])

    <!-- Attachements -->
    @include('partials.attachments', [
    'attachments' => $contact->attachments,
    'entityType' => 'Contacts',
    'entityId' => $contact->Contact_ID,
    ])

    <!-- Company Information -->

<div class="bg-white rounded-lg shadow p-6 mt-6">

    <h2 class="text-lg font-semibold mb-4">
        Company
    </h2>

    <div class="flex justify-between items-center border-b py-4">

        <div>
            <p class="font-medium">
                {{ $contact->company->Company_Name ?? 'Not Assigned' }}
            </p>
        </div>

        @if($contact->company)
            <a href="{{ route('customers.show', $contact->company->Company_ID) }}"
               class="text-cyan-600 hover:text-cyan-800 text-sm font-medium">
                View
            </a>
        @endif

    </div>

    <!-- Send Email Modal -->
    <div x-show="emailing"
         x-cloak
         @click.self="emailing = false"
         @keydown.escape.window="emailing = false"
         class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">

        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg">

            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">
                    Email {{ $contact->Contact_Name }}
                </h2>
                <button @click="emailing = false" type="button" class="text-gray-400 hover:text-gray-600 text-lg leading-none">✕</button>
            </div>

            @if(!$contact->Contact_Email)
                <p class="text-sm text-red-600">This contact has no email address on file — add one before sending.</p>
            @else
                <p class="text-sm text-gray-500 mb-3">To: {{ $contact->Contact_Email }}</p>

                <form method="POST" action="{{ route('emails.send') }}" class="space-y-2">
                    @csrf
                    <input type="hidden" name="Contact_ID" value="{{ $contact->Contact_ID }}">

                    <input
                        type="text"
                        name="Subject"
                        placeholder="Subject"
                        required
                        class="w-full border rounded-lg px-3 py-2 text-sm">

                    <textarea
                        name="Body"
                        rows="6"
                        placeholder="Write your message..."
                        required
                        class="w-full border rounded-lg px-3 py-2 text-sm"></textarea>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="emailing = false" class="px-4 py-2 rounded-lg bg-gray-200 text-sm">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 rounded-lg bg-cyan-600 text-white text-sm hover:bg-cyan-700">
                            Send Email
                        </button>
                    </div>
                </form>
            @endif

        </div>

    </div>

</div>

@endsection