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

<form action="{{ route('contacts.store') }}" method="POST">
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

    <div class="flex gap-2">

        <!-- Country Code -->
        <select
            name="Country_Code"
            class="w-32 border rounded-lg px-3 py-2">

            <option value="+60" selected>
                🇲🇾 +60
            </option>

            <option value="+66">
                🇹🇭 +66
            </option>

        </select>

        <!-- Phone Number -->
        <input
            type="text"
            name="Contact_No"
            placeholder="123456789"
            class="flex-1 border rounded-lg px-3 py-2">

    </div>
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

            <select
                name="Company_ID"
                required
                class="w-full border rounded-lg px-3 py-2">

                <option value="">
                    Select Company
                </option>

                @foreach($customers as $customer)
                    <option value="{{ $customer->Company_ID }}">
                        {{ $customer->Company_Name }}
                    </option>
                @endforeach

            </select>
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