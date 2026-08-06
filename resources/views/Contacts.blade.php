@extends('layouts.app')

@section('content')

<head> 
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>



<div class="flex justify-between items-center mb-6">

    <h1 class="text-2xl font-bold text-gray-800">
        Contacts
    </h1>

@unless(auth()->user()?->isGuest())
    <a href="{{ route('contacts.create') }}" class="px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700">
        + Add Contact
    </a>
@endunless

</div>

<!-- Search method -->
    <script>
        window.contactList = @json(
            \App\Models\Contact::pluck('Contact_Name')
                ->unique()
                ->values()

        );
    window.companyList = @json(
        \App\Models\Customer::pluck('Company_Name')->unique()->values()
    );
    </script>

<!-- Pinned Contacts -->
@if($pinnedContacts->count())
<div class="flex justify-between items-center mb-1">

    <h3 class="text-xl font-semibold text-gray-800">
        Pinned Contacts
    </h3>
</div>  

<div class="bg-white rounded-lg shadow overflow-hidden mb-6">

    <table class="w-full text-sm">

        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left">Name</th>
                <th class="px-6 py-3 text-left">Company</th>
                <th class="px-6 py-3 text-left">Email</th>
                <th class="px-6 py-3 text-left">Phone</th>
                <th class="px-6 py-3 text-left">Position</th>
            </tr>
        </thead>

        <tbody class="divide-y">

            @foreach($pinnedContacts as $contact)

            <tr
                onclick="window.location='{{ route('contacts.show', $contact->Contact_ID) }}'"
                class="cursor-pointer hover:bg-cyan-50">

                <td class="px-6 py-4">

                    <div class="flex items-center gap-2 group">

                        <span class="font-medium">
                        {{ $contact->Contact_Name }}
                        </span>

                        <form
                            action="{{ route('contacts.pin', $contact->Contact_ID) }}"
                            method="POST"
                            onclick="event.stopPropagation()">

                            @csrf

                            <button
                                type="submit"
                                title="Unpin contact"
                                class="text-lg hover:scale-110 transition">

                                📌

                            </button>

                        </form>

                    </div>

                <td class="px-6 py-4"> {{ $contact->company->Company_Name ?? 'N/A' }}</td>

                </td>

                <td class="px-6 py-4">
                    {{ $contact->Contact_Email }}
                </td>

                <td class="px-6 py-4">
                    {{ $contact->Country_Code }}
                    {{ $contact->Contact_No }}
                </td>

                <td class="px-6 py-4">
                    {{ $contact->Contact_Role }}
                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

</div>

@endif

<!-- Min content table -->
<div class="bg-white rounded-lg shadow overflow-hidden">

    <table class="w-full text-sm">

        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left">Name</th>
                <th class="px-6 py-3 text-left">Company</th>
                <th class="px-6 py-3 text-left">Email</th>
                <th class="px-6 py-3 text-left">Phone</th>
                <th class="px-6 py-3 text-left">Position</th>
            </tr>
        </thead>


        <tbody class="divide-y">

            @foreach($contacts as $contact)
        <tr
            onclick="window.location='{{ route('contacts.show', $contact->Contact_ID) }}'"
            class="cursor-pointer hover:bg-cyan-50">

            <td class="px-6 py-4"><div class="group flex items-center gap-2">

    <span>
        {{ $contact->Contact_Name }}
    </span>

    <form
        action="{{ route('contacts.pin', $contact->Contact_ID) }}"
        method="POST"
        onclick="event.stopPropagation()"
        class="relative">

        @csrf

        <button
            type="submit"
            title="{{ $contact->Is_Pinned ? 'Unpin' : 'Pin' }}"
            class="
                opacity-0
                group-hover:opacity-100
                transition
                duration-75
                hover:scale-110
                {{ $contact->Is_Pinned ? 'opacity-100' : 'opacity-0 group-hover:opacity-100' }}
                 transition duration-200 hover:scale-110
            ">

            {{ $contact->Is_Pinned ? '📌' : '📍' }}

        </button>

    </form>

</div></td>
            <td class="px-6 py-4"> {{ $contact->company->Company_Name ?? 'N/A' }}</td>
            <td class="px-6 py-4">{{ $contact->Contact_Email }}</td>
            <td class="px-6 py-4">{{ $contact->Country_Code }} {{ $contact->Contact_No ?? 'N/A' }}</td>
            <td class="px-6 py-4">{{ $contact->Contact_Role }}</td>    

        </tr>
        @endforeach
    </tbody>


    </table>

</div>

<div class="px-6 py-4 border-t">
   <div class="flex items-center justify-center gap-2 mt-6">

    @if ($contacts->onFirstPage())
        <span class="px-4 py-2 bg-gray-100 text-gray-400 rounded-lg">
            Previous
        </span>
    @else
        <a href="{{ $contacts->previousPageUrl() }}"
           class="px-4 py-2 bg-white border rounded-lg hover:bg-gray-50">
            Previous
        </a>
    @endif

    @for ($i = 1; $i <= $contacts->lastPage(); $i++)
        <a href="{{ $contacts->url($i) }}"
           class="px-4 py-2 rounded-lg
           {{ $contacts->currentPage() == $i
               ? 'bg-cyan-600 text-white'
               : 'bg-white border hover:bg-gray-50' }}">
            {{ $i }}
        </a>
    @endfor

    @if ($contacts->hasMorePages())
        <a href="{{ $contacts->nextPageUrl() }}"
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

