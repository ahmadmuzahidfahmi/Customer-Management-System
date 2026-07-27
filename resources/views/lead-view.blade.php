@extends('layouts.app')

@section('content')

<div x-data="{ emailing: false }" class="space-y-6">

    <!-- Header -->
    <div class="flex justify-between items-center">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                {{ $lead->Lead_Name }}
            </h1>

            <p class="text-gray-500">
                {{ $lead->Status ?? 'No Status' }}
            </p>
        </div>

        <div class="flex gap-2">

            <button
                type="button"
                @click="emailing = true"
                class="bg-gray-700 text-white px-4 py-2 rounded-lg hover:bg-gray-800">
                Send Email
            </button>

            <a href="{{ route('leads.edit', $lead->Lead_ID) }}"
               class="bg-cyan-600 text-white px-4 py-2 rounded-lg hover:bg-cyan-700">
                Edit Lead
            </a>

        <form method="POST"
            action="{{ route('leads.destroy', $lead->Lead_ID) }}"
            onsubmit="return confirm('Move this lead to Recycle Bin?');">

            @csrf
            @method('DELETE')

            <button
                type="submit"
                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                Delete
            </button>

        </form>

        </div>

    </div>

    <!-- Lead Details -->
    <div class="bg-white rounded-lg shadow p-6">

        <h2 class="text-lg font-semibold mb-4">
            Lead Information
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <p class="text-sm text-gray-500">
                    Lead Name
                </p>

                <p class="font-medium">
                    {{ $lead->Lead_Name }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">
                    Source
                </p>

                <p class="font-medium">
                    {{ $lead->Source }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">
                    Status
                </p>

                <p class="font-medium">
                    {{ $lead->Status ?? 'N/A' }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">
                    Assigned To
                </p>

                <p class="font-medium">
                    {{ $lead->user->User_Name ?? 'Unassigned' }}
                </p>
            </div>

        </div>

    </div>

    <!-- Company -->
    <div class="bg-white rounded-lg shadow p-6">

        <h2 class="text-lg font-semibold mb-4">
            Company
        </h2>

        <div class="flex justify-between items-center">

            <div>
                <p class="font-medium">
                    {{ $lead->company->Company_Name ?? 'No Company Assigned' }}
                </p>
            </div>

            @if($lead->company)

            <a href="{{ route('customers.show', $lead->company->Company_ID) }}"
               class="text-cyan-600 hover:text-cyan-800">
                View Company
            </a>

            @endif

        </div>

    </div>


<!-- Notes -->
@include('partials.notes', [
    'notes' => $lead->notes()->latest('Created_At')->get(),
    'ownerField' => 'Lead_ID',
    'ownerId' => $lead->Lead_ID,
])

@include('partials.activities', [
    'activities' => $lead->activities,
    'ownerField' => 'Lead_ID',
    'ownerId' => $lead->Lead_ID,
])

<!-- Send Email Modal -->
<div x-show="emailing"
     x-cloak
     @click.self="emailing = false"
     @keydown.escape.window="emailing = false"
     class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">

    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg">

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">
                Email {{ $lead->contact->Contact_Name ?? $lead->Lead_Name }}
            </h2>
            <button @click="emailing = false" type="button" class="text-gray-400 hover:text-gray-600 text-lg leading-none">✕</button>
        </div>

        @if(!($lead->contact->Contact_Email ?? null))
            <p class="text-sm text-red-600">This lead has no linked contact with an email address — link a contact before sending.</p>
        @else
            <p class="text-sm text-gray-500 mb-3">To: {{ $lead->contact->Contact_Email }}</p>

            <form method="POST" action="{{ route('emails.send') }}" class="space-y-2">
                @csrf
                <input type="hidden" name="Lead_ID" value="{{ $lead->Lead_ID }}">

                <input
                    type="text"
                    name="Subject"
                    placeholder="Subject"
                    required
                    class="w-full border rounded-lg px-3 py-2 text-sm">

                <textarea
                    name="Body"
                    rows="6"
                    placeholder="Write your message..."
                    required
                    class="w-full border rounded-lg px-3 py-2 text-sm"></textarea>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="emailing = false" class="px-4 py-2 rounded-lg bg-gray-200 text-sm">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-cyan-600 text-white text-sm hover:bg-cyan-700">
                        Send Email
                    </button>
                </div>
            </form>
        @endif

    </div>

</div>

</div>

@endsection