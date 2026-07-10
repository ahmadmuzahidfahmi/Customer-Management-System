@extends('layouts.app')

@section('content')

<h1 class="text-2xl font-bold mb-6">
    Edit Lead
</h1>

<div class="bg-white rounded-lg shadow p-6">

<form action="{{ route('leads.update', $lead->Lead_ID) }}"
      method="POST">

    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Lead Name -->
        <div>
            <label class="block text-sm font-medium mb-1">
                Lead Name
            </label>

            <input
                type="text"
                name="Lead_Name"
                value="{{ $lead->Lead_Name }}"
                class="w-full border rounded-lg px-3 py-2">
        </div>

        <!-- Company -->
        <div>
            <label class="block text-sm font-medium mb-1">
                Company
            </label>

            <select
                name="Company_ID"
                class="w-full border rounded-lg px-3 py-2">

                <option value="">
                    Select Company
                </option>

                @foreach($customers as $customer)

                    <option
                        value="{{ $customer->Company_ID }}"
                        {{ $lead->Company_ID == $customer->Company_ID ? 'selected' : '' }}>

                        {{ $customer->Company_Name }}

                    </option>

                @endforeach

            </select>
        </div>

        <!-- Source -->
        <div>
            <label class="block text-sm font-medium mb-1">
                Source
            </label>

            <input
                type="text"
                name="Source"
                value="{{ $lead->Source }}"
                class="w-full border rounded-lg px-3 py-2">
        </div>

        <!-- Assigned User -->
        <div>
            <label class="block text-sm font-medium mb-1">
                Assigned To
            </label>

            <select
                name="User_ID"
                class="w-full border rounded-lg px-3 py-2">

                <option value="">
                    Unassigned
                </option>

                @foreach($users as $user)

                    <option
                        value="{{ $user->User_ID }}"
                        
                          {{ $lead->User_ID == $user->User_ID ?  'Selected' : '' }}>

                        {{ $user->User_Name }}

                    </option>

                @endforeach

            </select>
        </div>

        <!-- Status -->
        <div>
            <label class="block text-sm font-medium mb-1">
                Status
            </label>

            <select
                name="Status"
                class="w-full border rounded-lg px-3 py-2">

                <option value="New"
                    {{ $lead->Status == 'New' ? 'selected' : '' }}>
                    New
                </option>

                <option value="Contacted"
                    {{ $lead->Status == 'Contacted' ? 'selected' : '' }}>
                    Contacted
                </option>

                <option value="Qualified"
                    {{ $lead->Status == 'Qualified' ? 'selected' : '' }}>
                    Qualified
                </option>

                <option value="Won"
                    {{ $lead->Status == 'Won' ? 'selected' : '' }}>
                    Won
                </option>

                <option value="Lost"
                    {{ $lead->Status == 'Lost' ? 'selected' : '' }}>
                    Lost
                </option>

            </select>
        </div>

        <!-- Notes -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-1">
                Notes
            </label>

            <textarea
                name="Lead_Note"
                rows="4"
                class="w-full border rounded-lg px-3 py-2">{{ $lead->Lead_Note }}</textarea>
        </div>

    </div>

    <div class="mt-6 flex gap-3">

        <button
            type="submit"
            class="px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700">
            Save Changes
        </button>

        <a href="{{ route('leads.show', $lead->Lead_ID) }}"
           class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
            Cancel
        </a>

    </div>

</form>

</div>

@endsection