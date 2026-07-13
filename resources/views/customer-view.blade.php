<head> 
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
@extends('layouts.app')

@section('content')

<div class="space-y-6">

    <!-- Header -->
    <div class="flex justify-between items-center">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                {{ $customer->Company_Name }}
            </h1>

            <p class="text-gray-500">
                {{ $customer->Status ?? 'Active' }}
            </p>
        </div>


<div class="flex gap-2">
    <a href="{{ route('customers.edit', $customer->Company_ID) }}"
       class="flex items-center justify-center w-36 h-12 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700">
        Edit Customer
    </a>

    <form action="{{ route('customers.destroy', $customer->Company_ID) }}"
          method="POST"
          onsubmit="return confirm('Move this customer to the Recycle Bin?');">

        @csrf
        @method('DELETE')

        <button
            type="submit"
            class="flex items-center justify-center w-36 h-12 bg-red-600 text-white rounded-lg hover:bg-red-700">
            Delete
        </button>
    </form>
</div>


    </div>

    <!-- Company Details -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">

        <h2 class="font-semibold text-lg mb-4">
            Company Information
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>
                <p class="text-sm text-gray-500">Company Name</p>
                <p>{{ $customer->Company_Name }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Email</p>
                <p>{{ $customer->Company_Email }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Phone</p>
                <p>{{ $customer->Company_No }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Status</p>
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
            </div>

        </div>

    </div>
    </div>

    <!-- Contacts -->
<div class="bg-white rounded-lg shadow p-6 mt-6">

    <h2 class="text-lg font-semibold text-gray-800 mb-4">
        Contacts
    </h2>

    @forelse($customer->contacts as $contact)

        <div class="border-b py-4 last:border-b-0">

            <div class="flex justify-between items-center">

                <div>
                    <p class="font-medium text-gray-800">
                        {{ $contact->Contact_Name }}
                    </p>

                    <p class="text-sm text-gray-500">
                        {{ $contact->Contact_Role ?? 'No Position' }}
                    </p>
                </div>

                <a href="{{ route('contacts.show', $contact->Contact_ID) }}"
                   class="text-cyan-600 hover:text-cyan-800 text-sm">
                    View
                </a>

            </div>

            <div class="mt-2 text-sm text-gray-600">

                <p>
                    📧 {{ $contact->Contact_Email ?? 'No Email' }}
                </p>

                <p>
                    📞 {{ $contact->Contact_No ?? 'No Phone' }}
                </p>

            </div>

        </div>

    @empty

        <div class="text-gray-500 text-sm py-4">
            No contacts found for this company.
        </div>

    @endforelse

</div>

<!-- Notes -->

<!-- Company Note -->
@include('partials.notes', [
    'notes' => $customer->notes()->latest('Created_At')->get(),
    'ownerField' => 'Company_ID',
    'ownerId' => $customer->Company_ID,
])

  <!-- Company Lead -->
<div class="bg-white rounded-lg shadow p-6 mt-6">

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-800">
            Related Leads
        </h2>

        <a href="{{ route('leads') }}"
           class="text-cyan-600 hover:text-cyan-700 text-sm">
            View All Leads
        </a>
    </div>

    @if($customer->leads->count())

        <div class="overflow-x-auto">
            <table class="w-full text-sm">

                <thead class="bg-gray-50">
                    <tr>
                <tr>
                    <th class="px-6 py-3 text-left">Lead Name</th>
                    <th class="px-6 py-3 text-left">Customer</th>
                    <th class="px-6 py-3 text-left">Value</th>
                    <th class="px-6 py-3 text-left">Source</th>
                    <th class="px-6 py-3 text-left">Last Update</th>
                    <th class="px-6 py-3 text-left">Status</th>
                </tr>
                    </tr>
                </thead>

                <tbody class="divide-y">

                    @foreach($customer->leads as $lead)

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
                    {{ $lead->Updated_At ?? 'unknown' }}
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

                </tr>
                @endforeach


                    </tr>

                </tbody>

            </table>
        </div>

    @else

        <div class="text-center py-8 text-gray-500">
            No leads linked to this customer.
        </div>

    @endif

</div>

<div class ="bg-white rounded-lg shadow p-6 mt-6">
    <h2 class="text-lg font-semibold text-gray-800">
    Activity Cards
    </h2>

</div>

<div class ="bg-white rounded-lg shadow p-6 mt-6">
    <h2 class="text-lg font-semibold text-gray-800">
Related Document
    </h2>

</div>

@endsection