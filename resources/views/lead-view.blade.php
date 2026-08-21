@extends('layouts.app')

@section('content')

<div x-data="{
    emailing: false,
    tab: '{{ request('tab', 'overview') }}',
    setTab(name) {
        this.tab = name;
        const url = new URL(window.location);
        url.searchParams.set('tab', name);
        window.history.replaceState({}, '', url);
    }
}" class="space-y-6">

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

        @unless(auth()->user()?->isGuest())
        <div class="flex gap-2">

            <a href="{{ route('leads.edit', $lead->Lead_ID) }}"
               class="flex items-center justify-center w-36 h-12 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700">
                Edit Lead
            </a>

            <form method="POST"
                  action="{{ route('leads.destroy', $lead->Lead_ID) }}"
                  onsubmit="return confirm('Move this lead to Recycle Bin?');">

                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="flex items-center justify-center w-36 h-12 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Delete
                </button>
            </form>
        </div>
        @endunless

    </div>

    <!-- Tabs -->
    <div class="flex gap-2 border-b overflow-x-auto">

        <button
            @click="setTab('overview')"
            :class="tab==='overview' ? 'border-cyan-600 text-cyan-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
            class="px-4 py-2 border-b-2 font-medium text-sm whitespace-nowrap">
            Overview
        </button>

        <button
            @click="setTab('notes')"
            :class="tab==='notes' ? 'border-cyan-600 text-cyan-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
            class="px-4 py-2 border-b-2 font-medium text-sm whitespace-nowrap">
            Notes ({{ $lead->notes()->count() }})
        </button>

        <button
            @click="setTab('activities')"
            :class="tab==='activities' ? 'border-cyan-600 text-cyan-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
            class="px-4 py-2 border-b-2 font-medium text-sm whitespace-nowrap">
            Activities ({{ $lead->activities()->count() }})
        </button>

        <button
            @click="setTab('attachments')"
            :class="tab==='attachments' ? 'border-cyan-600 text-cyan-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
            class="px-4 py-2 border-b-2 font-medium text-sm whitespace-nowrap">
            Attachments ({{ $lead->attachments->count() }})
        </button>

    </div>

    <!-- Overview Tab -->
    <div x-show="tab === 'overview'">

        <!-- Lead Information -->
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
                        {{ $lead->Source ?? 'N/A' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500 mb-1">
                        Status
                    </p>

                    @php
                        $statusColors = [
                            'New' => 'bg-gray-100 text-gray-700',
                            'Contacted' => 'bg-amber-100 text-amber-700',
                            'Qualified' => 'bg-blue-100 text-blue-700',
                            'Won' => 'bg-green-100 text-green-700',
                            'Lost' => 'bg-red-100 text-red-700',
                        ];
                    @endphp

                    <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$lead->Status] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ $lead->Status ?? 'N/A' }}
                    </span>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Assigned To
                    </p>

                    <p class="font-medium">
                        {{ $lead->user->User_Name ?? 'Unassigned' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Estimated Value
                    </p>

                    <p class="font-medium">
                        {{ $lead->Estimated_Value ?? 'N/A' }}
                    </p>
                </div>

            </div>

        </div>

        <!-- Company -->
        <div class="bg-white rounded-lg shadow p-6 mt-6">

            <h2 class="text-lg font-semibold mb-4">
                Company
            </h2>

            <div
                @if($lead->company)
                    onclick="window.location='{{ route('customers.show', $lead->company->Company_ID) }}'"
                @endif
                class="flex justify-between items-center border-b py-4
                    {{ $lead->company ? 'cursor-pointer hover:bg-cyan-50 transition rounded-lg px-3' : '' }}">

                <p class="font-medium">
                    {{ $lead->company->Company_Name ?? 'Not Assigned' }}
                </p>

            </div>

        </div>

        <!-- Contact -->
        <div class="bg-white rounded-lg shadow p-6 mt-6">

            <h2 class="text-lg font-semibold mb-4">
                Contact
            </h2>

            <div
                @if($lead->contact)
                    onclick="window.location='{{ route('contacts.show', $lead->contact->Contact_ID) }}'"
                @endif
                class="flex justify-between items-center border-b py-4
                    {{ $lead->contact ? 'cursor-pointer hover:bg-cyan-50 transition rounded-lg px-3' : '' }}">

                <p class="font-medium">
                    {{ $lead->contact->Contact_Name ?? 'Not Assigned' }}
                </p>

            </div>

        </div>

    </div>

    <!-- Notes Tab -->
    <div x-show="tab === 'notes'" x-cloak>
        @include('partials.notes', [
            'notes' => $lead->notes()->latest('Created_At')->get(),
            'ownerField' => 'Lead_ID',
            'ownerId' => $lead->Lead_ID,
        ])
    </div>

    <!-- Activities Tab -->
    <div x-show="tab === 'activities'" x-cloak>
        @include('partials.activities', [
            'activities' => $lead->activities,
            'ownerField' => 'Lead_ID',
            'ownerId' => $lead->Lead_ID,
        ])
    </div>

    <!-- Attachments Tab -->
    <div x-show="tab === 'attachments'" x-cloak>
        @include('partials.attachments', [
            'attachments' => $lead->attachments,
            'entityType' => 'Leads',
            'entityId' => $lead->Lead_ID,
        ])
    </div>

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
