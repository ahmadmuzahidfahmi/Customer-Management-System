<head> 
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />


</head>
@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Leads</h1>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <!-- Total Leads -->
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-cyan-500">
        <p class="text-sm text-gray-500">Total Leads</p>
        <p class="text-3xl font-bold text-cyan-600">
            {{ $totalLeads }}
        </p>
    </div>

    <!-- New Leads -->
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-blue-500">
        <p class="text-sm text-gray-500">New Leads</p>
        <p class="text-3xl font-bold text-blue-600">
            {{ $newLeads }}
        </p>
    </div>

    <!-- Contacted -->
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-amber-500">
        <p class="text-sm text-gray-500">Contacted</p>
        <p class="text-3xl font-bold text-amber-600">
            {{ $contactedLeads }}
        </p>
    </div>

    <!-- Won Leads -->
    <div class="bg-white rounded-xl shadow p-5 border-l-4 border-green-500">
        <p class="text-sm text-gray-500">Won Leads</p>
        <p class="text-3xl font-bold text-green-600">
            {{ $wonLeads }}
        </p>
    </div>

</div>

</div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b">
        <h2 class="text-lg font-semibold text-gray-800">
            Leads List
        </h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">

            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">Lead Name</th>
                    <th class="px-6 py-3 text-left">Customer</th>
                    <th class="px-6 py-3 text-left">Assigned To</th>
                    <th class="px-6 py-3 text-left">Last Update</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y">

                @foreach($leads as $lead)
                <tr>

                    <td class="px-6 py-4">
                        {{ $lead->Lead_Name }}
                    </td>

                    <td class="px-6 py-4">
                        {{ $lead->company->Company_Name ?? 'No Company' }}
                    </td>

                    <td class="px-6 py-4">
                        {{ $lead->User_ID ?? 'Unassigned' }}
                    </td>

                    <td class="px-6 py-4">
                    {{ $lead->Updated_At ?? 'Unassigned' }}
                    </td>

        <td class="px-6 py-4">

            @if($lead->Status == 'won')
                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                    Won
                </span>

            @elseif($lead->Status == 'Qualified')
                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
                    Qualified
                </span>

            @elseif($lead->Status == 'Contacted')
                <span class="px-2 py-1 text-xs rounded-full bg-amber-100 text-amber-700">
                    Contacted
                </span>

            @else
                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">
                    Lost
                </span>
            @endif
        </td>
                <td class="px-6 py-4">
                    <a href="{{ route('customers.show', $lead->company->Company_ID ?? '') }}"
                    class="text-cyan-600 hover:text-cyan-800">
                        View
                    </a>
                </td>

                </tr>
                @endforeach

            </tbody>

        </table>
    </div>

</div>
@endsection


