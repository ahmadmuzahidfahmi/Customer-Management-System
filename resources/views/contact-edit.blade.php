@extends('layouts.app')

@section('content')

<head> 
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />


</head>

<h1 class="text-2xl font-bold mb-6">
    Edit Contact
</h1>

<div class="bg-white rounded-lg shadow p-6">

<form action="{{ route('contacts.update', $contact->Contact_ID) }}"
      method="POST">

    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <div>
            <label class="block text-sm font-medium mb-1">
                Contact Name
            </label>

            <input
                type="text"
                name="Contact_Name"
                value="{{ $contact->Contact_Name }}"
                class="w-full border rounded-lg px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">
                Contact Email
            </label>

            <input
                type="email"
                name="Contact_Email"
                value="{{ $contact->Contact_Email }}"
                class="w-full border rounded-lg px-3 py-2">
        </div>

<div>
    <label class="block text-sm font-medium mb-1">
        Phone Number
    </label>

    @include('partials.phone-input', [
        'countryField' => 'Country_Code',
        'numberField' => 'Contact_No',
        'countries' => $countries,
        'selectedCode' => old('Country_Code', $contact->Country_Code ?? '+60'),
        'selectedNumber' => old('Contact_No', $contact->Contact_No),
    ])

</div>



                <div>
            <label class="block text-sm font-medium mb-1">
                Role
            </label>

            <input
                type="text"
                name="Contact_Role"
                value="{{ $contact->Contact_Role }}"
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
        'selectedId' => $contact->Company_ID,
    ])
</div>

    </div>

    <div class="mt-6 flex gap-3">

        <button
            type="submit"
            class="px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700">

            Save Changes

        </button>

        <a href="{{ route('contacts.show', $contact->Contact_ID) }}"
           class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">

            Cancel

        </a>

    </div>

</form>

</div>

@endsection