@extends('layouts.app')

@section('content')

<h1 class="text-2xl font-bold mb-6">
    Recycle Bin
</h1>

<div x-data="{ tab: 'customers' }">

<div class="flex gap-2 mb-6">

<button
    @click="tab='customers'"
    :class="tab==='customers'
        ? 'bg-cyan-600 text-white'
        : 'bg-white border text-gray-700'"
    class="px-4 py-2 rounded-lg">
    Customers ({{ $customers->count() }})
</button>

<button
    @click="tab='contacts'"
    :class="tab==='contacts'
        ? 'bg-cyan-600 text-white'
        : 'bg-white border text-gray-700'"
    class="px-4 py-2 rounded-lg">
    Contacts ({{ $contacts->count() }})
</button>

    <button
        @click="tab='leads'"
        :class="tab==='leads'
            ? 'bg-cyan-600 text-white'
            : 'bg-white border text-gray-700'"
        class="px-4 py-2 rounded-lg">
        Leads ({{ $deletedLeads->count() }})
    </button>

</div>

<!-- Customers -->
<div x-show="tab === 'customers'" class="space-y-3">

    @foreach($customers as $customer)

    <div
        class="bg-white rounded-lg shadow-sm border hover:shadow-md transition p-4">

        <div class="flex justify-between items-center">

            <div>

                <h3 class="font-semibold text-gray-800">
                    {{ $customer->Company_Name }}
                </h3>

                <p class="text-sm text-gray-500">
                    Customer •
                    Deleted {{ $customer->deleted_at->diffForHumans() }}
                </p>

            </div>

            <div class="flex gap-3">

                <button
    type="button"
    onclick="window.location='{{ route('customers.trashed.show', $customer->Company_ID) }}'"
    class="px-3 py-2 rounded-lg bg-cyan-100 text-cyan-700 hover:bg-cyan-200">
    View
</button>

                <form method="POST"
                    action="{{ route('customers.restore', $customer->Company_ID) }}">
                    @csrf

                    <button
                        class="px-3 py-2 rounded-lg bg-green-100 text-green-700 hover:bg-green-200">
                        Restore
                    </button>
                </form>

                <form method="POST"
                    action="{{ route('customers.forceDelete', $customer->Company_ID) }}">
                    @csrf
                    @method('DELETE')

                    <button
                        class="px-3 py-2 rounded-lg bg-red-100 text-red-700 hover:bg-red-200">
                        Delete Forever
                    </button>
                </form>

            </div>

        </div>

    </div>

    @endforeach

</div>

<!-- Contacts -->
<div x-show="tab === 'contacts'" class="space-y-3">

    @foreach($contacts as $contact)

    <div
        class="bg-white rounded-lg shadow-sm border hover:shadow-md transition p-4">

        <div class="flex justify-between items-center">

            <div>

                <h3 class="font-semibold text-gray-800">
                    {{ $contact->Contact_Name }}
                </h3>

                <p class="text-sm text-gray-500">
                    Contact •
                    Deleted {{ $contact->deleted_at->diffForHumans() }}
                </p>

            </div>

            <div class="flex gap-3">
                
<button
    type="button"
    onclick="window.location='{{ route('contacts.trashed.show', $contact->Contact_ID) }}'"
    class="px-3 py-2 rounded-lg bg-cyan-100 text-cyan-700 hover:bg-cyan-200">
    View
</button>

                <form method="POST"
                    action="{{ route('contacts.restore', $contact->Contact_ID) }}">
                    @csrf

                    <button
                        class="px-3 py-2 rounded-lg bg-green-100 text-green-700 hover:bg-green-200">
                        Restore
                    </button>
                </form>

                <form method="POST"
                    action="{{ route('contacts.forceDelete', $contact->Contact_ID) }}">
                    @csrf
                    @method('DELETE')

                    <button
                        class="px-3 py-2 rounded-lg bg-red-100 text-red-700 hover:bg-red-200">
                        Delete Forever
                    </button>
                </form>

            </div>

        </div>

    </div>

    @endforeach

</div>


<!-- Leads -->
<div x-show="tab === 'leads'" class="space-y-3">

    @foreach($deletedLeads as $lead)

    <div
        class="bg-white rounded-lg shadow-sm border hover:shadow-md transition p-4">

        <div class="flex justify-between items-center">

            <div>

                <h3 class="font-semibold text-gray-800">
                    {{ $lead->Lead_Name }}
                </h3>

                <p class="text-sm text-gray-500">
                    Lead •
                    Deleted {{ $lead->deleted_at->diffForHumans() }}
                </p>

            </div>

            <div class="flex gap-3">

                <button
    type="button"
    onclick="window.location='{{ route('leads.trashed.show', $lead->Lead_ID) }}'"
    class="px-3 py-2 rounded-lg bg-cyan-100 text-cyan-700 hover:bg-cyan-200">
    View
</button>

                <form method="POST"
                    action="{{ route('leads.restore', $lead->Lead_ID) }}">
                    @csrf

                    <button
                        class="px-3 py-2 rounded-lg bg-green-100 text-green-700 hover:bg-green-200">
                        Restore
                    </button>
                </form>

                <form method="POST"
                    action="{{ route('leads.forceDelete', $lead->Lead_ID) }}">
                    @csrf
                    @method('DELETE')

                    <button
                        class="px-3 py-2 rounded-lg bg-red-100 text-red-700 hover:bg-red-200">
                        Delete Forever
                    </button>
                </form>

            </div>

        </div>

    </div>

    @endforeach

</div>


</div>
@endsection
