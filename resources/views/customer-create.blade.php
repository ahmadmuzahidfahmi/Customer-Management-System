<head> 
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

@extends('layouts.app')

@section('content')

<div class="flex items-center gap-2 mb-6">
    <h1 class="text-3xl font-bold text-gray-800">
        Customers
    </h1>

    <span class="text-lg text-gray-500">
        / Add New Customer
    </span>
</div>

    <div class="bg-white rounded-lg shadow p-6">

<form action="{{ route('customers.store') }}" method="POST">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Company Name -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Company Name
            </label>

            <input
                type="text"
                name="Company_Name"
                required
                class="w-full border rounded-lg px-3 py-2">
        </div>

        <!-- Company Email -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Company Email
            </label>

            <input
                type="email"
                name="Company_Email"
                class="w-full border rounded-lg px-3 py-2">
        </div>

        <!-- Phone Number -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Phone Number
            </label>

            <div class="flex gap-2">

                <select
                    name="Country_Code"
                    class="border rounded-lg px-3 py-2">

                    <option value="+60">🇲🇾 +60</option>
                    <option value="+66">🇹🇭 +66</option>

                </select>

                <input
                    type="text"
                    name="Company_No"
                    placeholder="123456789"
                    class="flex-1 border rounded-lg px-3 py-2">

            </div>
        </div>

        <!-- Status -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Status
            </label>

            <select
                name="Status"
                class="w-full border rounded-lg px-3 py-2">

                <option value="Active">Active</option>
                <option value="Lead">Lead</option>
                <option value="Inactive">Inactive</option>

            </select>
        </div>

    </div>

    <div class="mt-6 flex gap-3">

        <button
            type="submit"
            class="px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700">
            Save Customer
        </button>

        <a href="{{ route('customers') }}"
           class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
            Back
        </a>

    </div>

</form>

    </div>

</div>

@endsection