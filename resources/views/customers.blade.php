@extends('layouts.app')

@section('content')
<head> 
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

@unless(auth()->user()?->isGuest())

<div class="flex justify-between items-center mb-6">

    <h1 class="text-2xl font-bold text-gray-800">
        Customers
    </h1>

        <a href="{{ route('customers.create') }}"
       class="px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700">
        + Add Customer
    </a>

</div>
@endunless 

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Total Customers</p>
        <p class="text-2xl font-bold text-cyan-600">
            {{ $customersCount }}
        </p>
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Active</p>
        <p class="text-2xl font-bold text-green-600">
            {{ $activeCustomers }}
        </p>
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <p class="text-sm text-gray-500">Inactive</p>
        <p class="text-2xl font-bold text-red-600">
            {{ $inactiveCustomers }}
        </p>
    </div>
</div>

<!-- Pinned Customers -->
@if($pinnedCustomers->count())
<div class="flex justify-between items-center mb-1">

    <h3 class="text-xl font-semibold text-gray-800">
        Pinned Customers
    </h3>
</div>  

<div class="bg-white rounded-lg shadow overflow-hidden mb-6">

    <table class="w-full text-sm">

        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left">Company</th>
                <th class="px-6 py-3 text-left">Email</th>
                <th class="px-6 py-3 text-left">Phone</th>
                <th class="px-6 py-3 text-left">Status</th>
            </tr>
        </thead>

        <tbody class="divide-y">

            @foreach($pinnedCustomers as $customer)

            <tr
                onclick="window.location='{{ route('customers.show', $customer->Company_ID) }}'"
                class="cursor-pointer hover:bg-cyan-50">

                <td class="px-6 py-4">

                    <div class="flex items-center gap-2 group">

                        <span class="font-medium">
                        {{ $customer->Company_Name }}
                        </span>

                        <form
                            action="{{ route('customers.pin', $customer->Company_ID) }}"
                            method="POST"
                            onclick="event.stopPropagation()">

                            @csrf

                            <button
                                type="submit"
                                title="Unpin customer"
                                class="text-lg hover:scale-110 transition">

                                📌

                            </button>

                        </form>

                    </div>
                </td>

                <td class="px-6 py-4">
                    {{ $customer->Company_Email }}
                </td>

                <td class="px-6 py-4">
                    {{ $customer->Country_Code }}
                    {{ $customer->Company_No }}
                </td>

                <td class="px-6 py-4">

                    @if($customer->Status == 'Active')
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                            Active
                        </span>

                    @elseif($customer->Status == 'Lead')
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
                            Lead
                        </span>

                    @else
                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">
                            Inactive
                        </span>
                    @endif

                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

</div>

@endif

<!-- Main content table -->
<div class="bg-white rounded-lg shadow overflow-hidden">

   <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[700px]">

        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left">Company</th>
                <th class="px-6 py-3 text-left">Email</th>
                <th class="px-6 py-3 text-left">Phone</th>
                <th class="px-6 py-3 text-left">Status</th>
            </tr>
        </thead>

<tbody class="divide-y divide-gray-100">
            
        @if($customers->count())

            @foreach($customers as $customer)

<tr
                onclick="window.location='{{ route('customers.show', $customer->Company_ID) }}'"
                class="group cursor-pointer hover:bg-cyan-50">

                <td class="px-6 py-4">

                    <div class="flex items-center gap-2">

                        <span>
                            {{ $customer->Company_Name }}
                        </span>

                        <form
                            action="{{ route('customers.pin', $customer->Company_ID) }}"
                            method="POST"
                            onclick="event.stopPropagation()"
                            class="relative">

                            @csrf

                            <button
                                type="submit"
                                title="{{ $pinnedCustomerIds->contains($customer->Company_ID) ? 'Unpin' : 'Pin' }}"
                                class="transition duration-200 hover:scale-110
                                {{ $pinnedCustomerIds->contains($customer->Company_ID) ? 'opacity-100' : 'opacity-0 group-hover:opacity-100' }}">
                                {{ $pinnedCustomerIds->contains($customer->Company_ID) ? '📌' : '📍' }}
                            </button>

                        </form>

                    </div>

                </td>

                <td class="px-6 py-4">
                    {{ $customer->Company_Email }}
                </td>

                <td class="px-6 py-4">
                    {{ $customer->Company_No ?? 'N/A' }}
                </td>

                <td class="px-6 py-4">
                    @if($customer->Status == 'Active')
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Active</span>
                    @elseif($customer->Status == 'Lead')
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">Lead</span>
                    @else
                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Inactive</span>
                    @endif
                </td>

            </tr>
            @endforeach

            @else

        <tr>
            <td colspan="4" class="py-10 text-center text-gray-500">
                No customers found.
            </td>
        </tr>

     @endif

        </tbody>

    </table>
    </div>

</div>
 <!-- end -->

 <!-- Pagination -->
 <div class="px-6 py-3 text-sm text-gray-500">

    Showing
    {{ $customers->firstItem() ?? 0 }}
    -
    {{ $customers->lastItem() ?? 0 }}
    of
    {{ $customers->total() }}
    customers

</div>

<div class="px-6 py-4 border-t">
   <div class="flex items-center justify-center gap-2 mt-6">

    @if ($customers->onFirstPage())
        <span class="px-4 py-2 bg-gray-100 text-gray-400 rounded-lg">
            Previous
        </span>
    @else
        <a href="{{ $customers->previousPageUrl() }}"
           class="px-4 py-2 bg-white border rounded-lg hover:bg-gray-50">
            Previous
        </a>
    @endif

    @for ($i = 1; $i <= $customers->lastPage(); $i++)
        <a href="{{ $customers->url($i) }}"
           class="px-4 py-2 rounded-lg
           {{ $customers->currentPage() == $i
               ? 'bg-cyan-600 text-white'
               : 'bg-white border hover:bg-gray-50' }}">
            {{ $i }}
        </a>
    @endfor

    @if ($customers->hasMorePages())
        <a href="{{ $customers->nextPageUrl() }}"
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
