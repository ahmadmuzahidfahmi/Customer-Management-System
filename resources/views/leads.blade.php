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

    <div class="flex gap-2">
        <a href="{{ route('leads.kanban') }}"
           class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
            Board View
        </a>
    @unless(auth()->user()?->isGuest())
        <a href="{{ route('leads.create') }}"
           class="bg-cyan-600 text-white px-4 py-2 rounded-lg hover:bg-cyan-700">
            + Add Lead
        </a>
    @endunless
    </div>
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

<!-- Pinned Leads -->
@if($pinnedLeads->count())
<div class="flex justify-between items-center mb-1">

    <h3 class="text-xl font-semibold text-gray-800">
        Pinned Leads
    </h3>
</div>  

<div class="bg-white rounded-lg shadow overflow-hidden mb-6">

    <table class="w-full text-sm">

        <thead class="bg-gray-50">
             <tr>
                <th class="px-6 py-3 text-left">Lead Name</th>
                <th class="px-6 py-3 text-left">Customer</th>
                <th class="px-6 py-3 text-left">Value</th>
                <th class="px-6 py-3 text-left">Source</th>
                <th class="px-6 py-3 text-left">Status</th>
                <th class="px-6 py-3 text-left">Assigned To</th>
            </tr>
        </thead>

        <tbody class="divide-y">

            @foreach($pinnedLeads as $lead)

            <tr
                onclick="window.location='{{ route('leads.show', $lead->Lead_ID) }}'"
                class="cursor-pointer hover:bg-cyan-50">

                <td class="px-6 py-4">

                    <div class="flex items-center gap-2 group">

                        <span class="font-medium">
                        {{ $lead->Lead_Name }}
                        </span>

                        <form
                            action="{{ route('leads.pin', $lead->Lead_ID) }}"
                            method="POST"
                            onclick="event.stopPropagation()">

                            @csrf

                            <button
                                type="submit"
                                title="Unpin lead"
                                class="text-lg hover:scale-110 transition">

                                📌

                            </button>

                        </form>

                    </div>
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

                        @if($lead->Status == 'New')

                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                                New
                            </span>

                        @elseif($lead->Status == 'Won')

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

            </tr>

            @endforeach

        </tbody>

    </table>

</div>

@endif

<div class="bg-white rounded-lg shadow overflow-hidden">

    <table class="w-full text-sm">

        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left">Lead Name</th>
                <th class="px-6 py-3 text-left">Customer</th>
                <th class="px-6 py-3 text-left">Value</th>
                <th class="px-6 py-3 text-left">Source</th>
                <th class="px-6 py-3 text-left">Status</th>
                <th class="px-6 py-3 text-left">Assigned To</th>
            </tr>
        </thead>

        <tbody class="divide-y">

            @foreach($leads as $lead)

<tr
                    onclick="window.location='{{ route('leads.show', $lead->Lead_ID) }}'"
                    class="group cursor-pointer hover:bg-cyan-50">

                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">

                            <span>
                                {{ $lead->Lead_Name }}
                            </span>

                            <form
                                action="{{ route('leads.pin', $lead->Lead_ID) }}"
                                method="POST"
                                onclick="event.stopPropagation()"
                                class="relative">

                                @csrf

                                <button
                                    type="submit"
                                    title="{{ $pinnedLeadIds->contains($lead->Lead_ID) ? 'Unpin' : 'Pin' }}"
                                    class="transition duration-200 hover:scale-110
                                    {{ $pinnedLeadIds->contains($lead->Lead_ID) ? 'opacity-100' : 'opacity-0 group-hover:opacity-100' }}">
                                    {{ $pinnedLeadIds->contains($lead->Lead_ID) ? '📌' : '📍' }}
                                </button>

                            </form>

                        </div>
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

                        @if($lead->Status == 'New')

                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                                New
                            </span>

                        @elseif($lead->Status == 'Won')

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

                </tr>

            @endforeach

        </tbody>

    </table>


</div>

    {{-- Pagination --}}
    <div class="px-6 py-4 border-t">

        <div class="flex items-center justify-center gap-2">

            @php
                $current = $leads->currentPage();
                $last = $leads->lastPage();
            @endphp

            {{-- Previous --}}
            @if ($leads->onFirstPage())

                <span class="px-4 py-2 bg-gray-100 text-gray-400 rounded-lg">
                    Previous
                </span>

            @else

                <a
                    href="{{ $leads->previousPageUrl() }}"
                    class="px-4 py-2 bg-white border rounded-lg hover:bg-gray-50">

                    Previous

                </a>

            @endif


            @if($last > 1)

                {{-- First Page --}}
                <a
                    href="{{ $leads->url(1) }}"
                    class="px-4 py-2 rounded-lg
                    {{ $current == 1
                        ? 'bg-cyan-600 text-white'
                        : 'bg-white border hover:bg-gray-50' }}">

                    1

                </a>


                {{-- Left Dots --}}
                @if($current > 3)

                    <span class="px-2 text-gray-500">
                        ...
                    </span>

                @endif


                {{-- Nearby Pages --}}
                @for(
                    $i = max(2, $current - 1);
                    $i <= min($last - 1, $current + 1);
                    $i++
                )

                    <a
                        href="{{ $leads->url($i) }}"
                        class="px-4 py-2 rounded-lg
                        {{ $current == $i
                            ? 'bg-cyan-600 text-white'
                            : 'bg-white border hover:bg-gray-50' }}">

                        {{ $i }}

                    </a>

                @endfor


                {{-- Right Dots --}}
                @if($current < $last - 2)

                    <span class="px-2 text-gray-500">
                        ...
                    </span>

                @endif


                {{-- Last Page --}}
                @if($last > 1)

                    <a
                        href="{{ $leads->url($last) }}"
                        class="px-4 py-2 rounded-lg
                        {{ $current == $last
                            ? 'bg-cyan-600 text-white'
                            : 'bg-white border hover:bg-gray-50' }}">

                        {{ $last }}

                    </a>

                @endif

            @endif


            {{-- Next --}}
            @if ($leads->hasMorePages())

                <a
                    href="{{ $leads->nextPageUrl() }}"
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


