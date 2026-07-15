@extends('layouts.app')

@section('content')
<head> 
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />


</head>

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
        x-show="$store.search.query === '' || {{ Js::from(strtolower($customer->Company_Name.' '.($customer->Status ?? ''))) }}.includes($store.search.query.toLowerCase())"
        onclick="window.location='{{ route('customers.trashed.show', $customer->Company_ID) }}'"
        class="group bg-white rounded-lg shadow-sm border hover:shadow-md transition p-4 cursor-pointer">

        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-semibold text-gray-800">{{ $customer->Company_Name }}</h3>
                <p class="text-sm text-gray-500">
                    Customer • Deleted {{ $customer->deleted_at->diffForHumans() }}
                </p>
            </div>

            <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity" onclick="event.stopPropagation()">

                <form method="POST" action="{{ route('customers.restore', $customer->Company_ID) }}">
                    @csrf
                    <button
                        type="submit"
                        title="Restore"
                        class="w-9 h-9 flex items-center justify-center rounded-lg bg-green-100 text-green-700 hover:bg-green-200">
                        ↺
                    </button>
                </form>

                <form method="POST" action="{{ route('customers.forceDelete', $customer->Company_ID) }}">
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        title="Delete Forever"
                        class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-100 text-red-700 hover:bg-red-200">
                        🗑
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
        x-show="$store.search.query === '' || {{ Js::from(strtolower($contact->Contact_Name.' '.($contact->Status ?? ''))) }}.includes($store.search.query.toLowerCase())"
        onclick="window.location='{{ route('contacts.trashed.show', $contact->Contact_ID) }}'"
        class="group bg-white rounded-lg shadow-sm border hover:shadow-md transition p-4 cursor-pointer">

        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-semibold text-gray-800">{{ $contact->Contact_Name }}</h3>
                <p class="text-sm text-gray-500">
                    Contact • Deleted {{ $contact->deleted_at->diffForHumans() }}
                </p>
            </div>

            <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity" onclick="event.stopPropagation()">

                <form method="POST" action="{{ route('contacts.restore', $contact->Contact_ID) }}">
                    @csrf
                    <button
                        type="submit"
                        title="Restore"
                        class="w-9 h-9 flex items-center justify-center rounded-lg bg-green-100 text-green-700 hover:bg-green-200">
                        ↺
                    </button>
                </form>

                <form method="POST" action="{{ route('contacts.forceDelete', $contact->Contact_ID) }}">
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        title="Delete Forever"
                        class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-100 text-red-700 hover:bg-red-200">
                        🗑
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
        x-show="$store.search.query === '' || {{ Js::from(strtolower($lead->Lead_Name.' '.($lead->Source ?? ''))) }}.includes($store.search.query.toLowerCase())"
        onclick="window.location='{{ route('leads.trashed.show', $lead->Lead_ID) }}'"
        class="group bg-white rounded-lg shadow-sm border hover:shadow-md transition p-4 cursor-pointer">

        <div class="flex justify-between items-center">

            <div>
                <h3 class="font-semibold text-gray-800">{{ $lead->Lead_Name }}</h3>
                <p class="text-sm text-gray-500">
                    Lead • Deleted {{ $lead->deleted_at->diffForHumans() }}
                </p>
            </div>

            <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity" onclick="event.stopPropagation()">

                <form method="POST" action="{{ route('leads.restore', $lead->Lead_ID) }}">
                    @csrf
                    <button
                        type="submit"
                        title="Restore"
                        class="w-9 h-9 flex items-center justify-center rounded-lg bg-green-100 text-green-700 hover:bg-green-200">
                        ↺
                    </button>
                </form>

                <form method="POST" action="{{ route('leads.forceDelete', $lead->Lead_ID) }}">
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        title="Delete Forever"
                        class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-100 text-red-700 hover:bg-red-200">
                        🗑
                    </button>
                </form>

            </div>

        </div>

    </div>

    @endforeach

</div>
@endsection
