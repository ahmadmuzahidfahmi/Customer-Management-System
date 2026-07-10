@extends('layouts.app')

@section('content')
<head> 
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />


</head>

<script>
    window.leadList = @json(
        \App\Models\Leads::pluck('Lead_Name')->unique()->values()
    );

    window.sourceList = @json(
        \App\Models\Leads::pluck('Source')->unique()->values()
    );
</script>

<head> 
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />


</head>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Leads</h1>

    <a href="{{ route('leads.create') }}"
   class="bg-cyan-600 text-white px-4 py-2 rounded-lg hover:bg-cyan-700">
    + Add Lead
</a>
</div>


<div class="bg-white rounded-lg shadow p-4 mb-6">

<form method="GET"
      action="{{ route('leads') }}"
      class="grid grid-cols-1 md:grid-cols-3 gap-4">

    <!-- Lead Search -->
    <div class="relative"
         x-data="searchDropdown('leadList')">

        <label class="block text-sm font-medium text-gray-700 mb-1">
            Search Lead
        </label>

        <input
            type="text"
            name="lead"
            placeholder="Type lead name..."
            value="{{ request('lead') }}"
            class="w-full border rounded-lg px-3 py-2"
            x-model="query"
            @input="filterResults()"
            @focus="open = true"
            @click.away="open = false">

        <div
            x-show="open && filtered.length > 0"
            class="absolute z-50 bg-white border w-full mt-1 rounded-lg shadow">

            <template x-for="item in filtered" :key="item">

                <div
                    class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
                    @click="select(item)"
                    x-text="item">

                </div>

            </template>

        </div>

    </div>

    <!-- Source Search -->
    <div class="relative"
         x-data="searchDropdown('sourceList')">

        <label class="block text-sm font-medium text-gray-700 mb-1">
            Source
        </label>

        <input
            type="text"
            name="source"
            placeholder="Type source..."
            value="{{ request('source') }}"
            class="w-full border rounded-lg px-3 py-2"
            x-model="query"
            @input="filterResults()"
            @focus="open = true"
            @click.away="open = false">

        <div
            x-show="open && filtered.length > 0"
            class="absolute z-50 bg-white border w-full mt-1 rounded-lg shadow">

            <template x-for="item in filtered" :key="item">

                <div
                    class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
                    @click="select(item)"
                    x-text="item">

                </div>

            </template>

        </div>

    </div>

    <!-- Buttons -->
    <div class="flex items-end gap-2">

        <button
            type="submit"
            class="bg-cyan-600 text-white px-4 py-2 rounded-lg hover:bg-cyan-700">

            Search

        </button>

        <a href="{{ route('leads') }}"
           class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">

            Reset

        </a>

    </div>

</form>

</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <!-- Total Leads -->
    <div class="bg-white rounded-xl shadow p-5 border-l-4">
        <p class="text-sm text-gray-500">Total Leads</p>
        <p class="text-3xl font-bold text-cyan-600">
            {{ $totalLeads }}
        </p>
    </div>

    <!-- New Leads -->
    <div class="bg-white rounded-xl shadow p-5 border-l-4">
        <p class="text-sm text-gray-500">New Leads</p>
        <p class="text-3xl font-bold text-blue-600">
            {{ $newLeads }}
        </p>
    </div>

    <!-- Contacted -->
    <div class="bg-white rounded-xl shadow p-5 border-l-4 ">
        <p class="text-sm text-gray-500">Contacted</p>
        <p class="text-3xl font-bold text-amber-600">
            {{ $contactedLeads }}
        </p>
    </div>

    <!-- Won Leads -->
    <div class="bg-white rounded-xl shadow p-5 border-l-4">
        <p class="text-sm text-gray-500">Won Leads</p>
        <p class="text-3xl font-bold text-green-600">
            {{ $wonLeads }}
        </p>
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
                    <th class="px-6 py-3 text-left">Value</th>
                    <th class="px-6 py-3 text-left">Source</th>
                    <th class="px-6 py-3 text-left">Last Update</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Assigned To</th>
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
                        {{ $lead->Estimated_Value ?? 'unknown' }}
                    </td>

                    <td class="px-6 py-4">
                        {{ $lead->Source ?? 'unknown' }}
                    </td>

                    <td class="px-6 py-4">
                    {{ $lead->Updated_At ?? 'Unassigned' }}
                    </td>

        <td class="px-6 py-4">

                        
            @if($lead->Status == 'New')
                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                    New
            </span>

            @elseif($lead->Status == 'won')
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
                        {{ $lead->user->User_Name ?? 'Unassigned' }}
                        </td>

                <td class="px-6 py-4">
                    <a href="{{ route('leads.show', $lead->Lead_ID) }}"
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

<div class="px-6 py-4 border-t">
   <div class="flex items-center justify-center gap-2 mt-6">

    @if ($leads->onFirstPage())
        <span class="px-4 py-2 bg-gray-100 text-gray-400 rounded-lg">
            Previous
        </span>
    @else
        <a href="{{ $leads->previousPageUrl() }}"
           class="px-4 py-2 bg-white border rounded-lg hover:bg-gray-50">
            Previous
        </a>
    @endif

    @for ($i = 1; $i <= $leads->lastPage(); $i++)
        <a href="{{ $leads->url($i) }}"
           class="px-4 py-2 rounded-lg
           {{ $leads->currentPage() == $i
               ? 'bg-cyan-600 text-white'
               : 'bg-white border hover:bg-gray-50' }}">
            {{ $i }}
        </a>
    @endfor

    @if ($leads->hasMorePages())
        <a href="{{ $leads->nextPageUrl() }}"
           class="px-4 py-2 bg-white border rounded-lg hover:bg-gray-50">
            Next
        </a>
    @else
        <span class="px-4 py-2 bg-gray-100 text-gray-400 rounded-lg">
            Next
        </span>
    @endif

</div>
</div>
@endsection


