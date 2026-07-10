@extends('layouts.app')

@section('content')

<div class="space-y-6">

    <!-- Header -->
    <div class="flex justify-between items-center">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                {{ $lead->Lead_Name }}
            </h1>

            <p class="text-gray-500">
                {{ $lead->Status ?? 'No Status' }}
            </p>
        </div>

        <div class="flex gap-2">

            <a href="{{ route('leads.edit', $lead->Lead_ID) }}"
               class="bg-cyan-600 text-white px-4 py-2 rounded-lg hover:bg-cyan-700">
                Edit Lead
            </a>

        <form method="POST"
            action="{{ route('leads.destroy', $lead->Lead_ID) }}"
            onsubmit="return confirm('Move this lead to Recycle Bin?');">

            @csrf
            @method('DELETE')

            <button
                type="submit"
                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                Delete
            </button>

        </form>

        </div>

    </div>

    <!-- Lead Details -->
    <div class="bg-white rounded-lg shadow p-6">

        <h2 class="text-lg font-semibold mb-4">
            Lead Information
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <p class="text-sm text-gray-500">
                    Lead Name
                </p>

                <p class="font-medium">
                    {{ $lead->Lead_Name }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">
                    Source
                </p>

                <p class="font-medium">
                    {{ $lead->Source }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">
                    Status
                </p>

                <p class="font-medium">
                    {{ $lead->Status ?? 'N/A' }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">
                    Assigned To
                </p>

                <p class="font-medium">
                    {{ $lead->user->User_Name ?? 'Unassigned' }}
                </p>
            </div>

        </div>

    </div>

    <!-- Company -->
    <div class="bg-white rounded-lg shadow p-6">

        <h2 class="text-lg font-semibold mb-4">
            Company
        </h2>

        <div class="flex justify-between items-center">

            <div>
                <p class="font-medium">
                    {{ $lead->company->Company_Name ?? 'No Company Assigned' }}
                </p>
            </div>

            @if($lead->company)

            <a href="{{ route('customers.show', $lead->company->Company_ID) }}"
               class="text-cyan-600 hover:text-cyan-800">
                View Company
            </a>

            @endif

        </div>

    </div>

    <!-- Notes -->
    <div class="bg-white rounded-lg shadow p-6">

        <h2 class="text-lg font-semibold mb-4">
            Notes
        </h2>

        <p class="text-gray-700">
            {{ $lead->Lead_Note ?? 'No notes available.' }}
        </p>

    </div>

</div>

@endsection