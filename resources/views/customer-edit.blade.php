<head> 
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

@extends('layouts.app')

@section('content')

<div class="flex items-center gap-2 mb-6">
    <h1 class="text-3xl font-bold text-gray-800">
        {{ $customer->Company_Name }}
    </h1>

    <span class="text-lg text-gray-500">
        / Edit
    </span>
</div>

<form action="{{ route('customers.update', $customer->Company_ID) }}" method="POST">
    @csrf
    @method('PUT')

    <!-- Company Details -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Company Details</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Company Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Company Name
                </label>

                <input
                    type="text"
                    name="Company_Name"
                    value="{{ old('Company_Name', $customer->Company_Name) }}"
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
                    value="{{ old('Company_Email', $customer->Company_Email) }}"
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
                        class="border rounded-lg px-3 py-2 w-40">

                        @foreach($countries as $country)
                            <option
                                value="{{ $country['code'] }}"
                                {{ old('Country_Code', $customer->Country_Code) == $country['code'] ? 'selected' : '' }}>
                                {{ $country['name'] }} ({{ $country['code'] }})
                            </option>
                        @endforeach

                    </select>

                    <input
                        type="text"
                        name="Company_No"
                        value="{{ old('Company_No', $customer->Company_No) }}"
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

                    <option value="Active" {{ old('Status', $customer->Status) == 'Active' ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="Lead" {{ old('Status', $customer->Status) == 'Lead' ? 'selected' : '' }}>
                        Lead
                    </option>

                    <option value="Inactive" {{ old('Status', $customer->Status) == 'Inactive' ? 'selected' : '' }}>
                        Inactive
                    </option>

                </select>
            </div>

        </div>
    </div>

    <!-- Actions -->
    <div class="flex gap-3">

        <button
            type="submit"
            class="px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700">
            Save Changes
        </button>

        <a href="{{ route('customers.show', $customer->Company_ID) }}"
           class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
            Cancel
        </a>

    </div>

</form>

@endsection