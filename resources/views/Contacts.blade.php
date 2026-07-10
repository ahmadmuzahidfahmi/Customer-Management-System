<head> 
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

@extends('layouts.app')

@section('content')

<div class="flex justify-between items-center mb-6">

    <h1 class="text-2xl font-bold text-gray-800">
        Contacts
    </h1>

    <a href="{{ route('contacts.create') }}" class="px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700">
        + Add Contact
    </a>

</div>

<div class="bg-white rounded-lg shadow p-4 mb-6">

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
        <form method="GET"
          action="{{ route('contacts') }}"
          class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- Contact Search -->
        <div class="relative" x-data="searchDropdown(window.contactList)">

            <label class="block text-sm font-medium text-gray-700 mb-1">
                Search Contact
            </label>

            <input
                type="text"
                name="contact"
                placeholder="Type contact name..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-cyan-500"
                x-model="query"
                @input="filterResults()"
                @focus="open = true"
                @click.away="open = false"
                autocomplete="off"
            >

            <div
                x-show="open && filtered.length"
                class="absolute z-50 bg-white border w-full mt-1 rounded-lg shadow-lg max-h-48 overflow-y-auto">

                <template x-for="item in filtered" :key="item">

                    <div
                        class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
                        @click="select(item)"
                        x-text="item">
                    </div>

                </template>

            </div>

        </div>

        <!-- Company Search -->
        <div class="relative" x-data="searchDropdown(window.companyList)">

            <label class="block text-sm font-medium text-gray-700 mb-1">
                Company
            </label>

            <input
                type="text"
                name="company"
                placeholder="Type company name..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-cyan-500"
                x-model="query"
                @input="filterResults()"
                @focus="open = true"
                @click.away="open = false"
                autocomplete="off"
            >

            <div
                x-show="open && filtered.length"
                class="absolute z-50 bg-white border w-full mt-1 rounded-lg shadow-lg max-h-48 overflow-y-auto">

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

            <a href="{{ route('contacts') }}"
               class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">

                Reset

            </a>

        </div>

    </form>

</div>

<div class="bg-white rounded-lg shadow overflow-hidden">

    <table class="w-full text-sm">

        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left">Name</th>
                <th class="px-6 py-3 text-left">Company</th>
                <th class="px-6 py-3 text-left">Email</th>
                <th class="px-6 py-3 text-left">Phone</th>
                <th class="px-6 py-3 text-left">Position</th>
                <th class="px-6 py-3 text-left">Actions</th>
            </tr>
        </thead>

        <tbody class="divide-y">

            @foreach($contacts as $contact)

            <tr>

                <td class="px-6 py-4">
                    {{ $contact->Contact_Name }}
                </td>

                <td class="px-6 py-4">
                    {{ $contact->company->Company_Name ?? 'N/A' }}
                </td>

                <td class="px-6 py-4">
                    {{ $contact->Contact_Email }}
                </td>

                <td class="px-6 py-4">
                    {{ $contact->Country_Code }} {{ $contact->Contact_No ?? 'N/A' }}
                </td>

                <td class="px-6 py-4">
                    {{ $contact->Contact_Role }}
                </td>

                <td class="px-6 py-4">
                    <a href="{{ route('contacts.show', $contact->Contact_ID) }}"
                        class="text-cyan-600 hover:text-cyan-800">
                            View
                    </a>
                </td>

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

