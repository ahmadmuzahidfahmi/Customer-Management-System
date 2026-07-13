<head> 
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

@extends('layouts.app')

@section('content')

<div class="max-w-4xl mx-auto space-y-6">

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
                <p class="font-medium">
                    {{ $contact->Country_Code }} {{ $contact->Contact_No ?? 'N/A' }}
                </p>
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

</div>

@endsection