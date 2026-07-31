@extends('layouts.app')

@section('content')
<head> 
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />


</head>

<h1 class="text-2xl font-bold mb-6">
    Edit Customer
</h1>

<div class="bg-white rounded-lg shadow p-6">

<form action="{{ route('customers.update', $customer->Company_ID) }}"
      method="POST">

    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <div>
            <label class="block text-sm font-medium mb-1">
                Company Name
            </label>

            <input
                type="text"
                name="Company_Name"
                value="{{ $customer->Company_Name }}"
                class="w-full border rounded-lg px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">
                Company Email
            </label>

            <input
                type="email"
                name="Company_Email"
                value="{{ $customer->Company_Email }}"
                class="w-full border rounded-lg px-3 py-2">
        </div>

<div>
    <label class="block text-sm font-medium mb-1">
        Phone Number
    </label>

    <div class="flex gap-2">

        <select
            name="Country_Code"
            class="border rounded-lg px-3 py-2 w-40">

            @foreach($countries as $country)

                <option
                    value="{{ $country['code'] }}"
                    {{ $customer->Country_Code == $country['code'] ? 'selected' : '' }}>

                    {{ $country['name'] }}
                    ({{ $country['code'] }})

                </option>

            @endforeach

        </select>


        <input
            type="text"
            name="Company_No"
            value="{{ $customer->Company_No }}"
            placeholder="123456789"
            class="flex-1 border rounded-lg px-3 py-2">

    </div>

</div>

        <div>
            <label class="block text-sm font-medium mb-1">
                Status
            </label>

            <select
                name="Status"
                class="w-full border rounded-lg px-3 py-2">

                <option value="Active"
                    {{ $customer->Status == 'Active' ? 'selected' : '' }}>
                    Active
                </option>

                <option value="Lead"
                    {{ $customer->Status == 'Lead' ? 'selected' : '' }}>
                    Lead
                </option>

                <option value="Inactive"
                    {{ $customer->Status == 'Inactive' ? 'selected' : '' }}>
                    Inactive
                </option>

            </select>

        </div>

                <div>
            <label class="block text-sm font-medium mb-1">
                Note
            </label>

            <input
                type="text"
                name="Company_Note"
                value="{{ $customer->Company_Note }}"
                class="w-full border rounded-lg px-3 py-2">
        </div>

    </div>

    <div class="mt-6 flex gap-3">

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

</div>

@endsection