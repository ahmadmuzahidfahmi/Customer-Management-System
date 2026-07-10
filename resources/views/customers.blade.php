@extends('layouts.app')

@section('content')
<head> 
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />


</head>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Customers</h1>

    <a href="{{ route('customers.create') }}" class="px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700">
        + Add Customer
    </a>
</div>


<div class="bg-white rounded-lg shadow p-4 mb-6">
    <script>
    window.companyList = @json(\App\Models\Customer::pluck('company_name')->unique()->values());
</script>



<!-- Customer Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">

    <div class="px-6 py-4 border-b">
        <h2 class="text-lg font-semibold text-gray-800">
            Customer List
        </h2>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">

            <!-- Table Header -->
<thead class="bg-gray-50 text-gray-600 uppercase text-xs">
    <tr>
        <!-- Customer (always visible) -->
        <th class="px-6 py-3">Customer</th>

        <!-- Email (hidden on mobile, visible on md+) -->
        <th class="px-6 py-3 hidden md:table-cell">Email</th>

        <!-- Company (always visible) -->
        <th class="px-6 py-3 hidden md:table-cell">Phone</th>

        <!-- Status (always visible) -->
        <th class="px-6 py-3">Status</th>

        <!-- Actions (always visible) -->
        <th class="px-6 py-3">Actions</th>
    </tr>
</thead>


            <!-- Table Body -->
<tbody class="divide-y divide-gray-200">
@foreach($customers as $customer)
    <tr class="hover:bg-gray-50">

        <td class="px-6 py-4 font-medium text-gray-900">
            {{ $customer->Company_Name }}
        </td>

<td class="px-6 py-4 hidden md:table-cell">
    {{ $customer->Company_Email }}
</td>

        <td class="px-6 py-4 hidden md:table-cell">
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

        <td class="px-6 py-4">
            <a href="{{ route('customers.show', $customer->Company_ID) }}"
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

<div class="flex items-center justify-between px-6 py-4">
<p class="text-sm text-gray-500">
    Showing {{ $customers->firstItem() }}–{{ $customers->lastItem() }}
    of {{ $customers->total() }} customers
</p>
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
