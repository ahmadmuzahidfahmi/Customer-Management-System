<head> 
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

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
                {{ $contact->Contact_Name }}
            </h1>

            <p class="text-gray-500">
                {{ $contact->Contact_Role ?? 'Contact' }}
            </p>
        </div>

        @unless(auth()->user()?->isGuest())
        <div class="flex gap-2">

            <button
                type="button"
                @click="emailing = true"
                class="flex items-center justify-center w-36 h-12 bg-gray-700 text-white rounded-lg hover:bg-gray-800">
                Send Email
            </button>

            <a href="{{ route('contacts.edit', $contact->Contact_ID) }}"
               class="flex items-center justify-center w-36 h-12 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700">
                Edit Contact
            </a>

            <form action="{{ route('contacts.destroy', $contact->Contact_ID) }}"
                  method="POST"
                  onsubmit="return confirm('Move this contact to the Recycle Bin?');">

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
            @click="setTab('leads')"
            :class="tab==='leads' ? 'border-cyan-600 text-cyan-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
            class="px-4 py-2 border-b-2 font-medium text-sm whitespace-nowrap">
            Leads ({{ $contact->leads->count() }})
        </button>

        <button
            @click="setTab('notes')"
            :class="tab==='notes' ? 'border-cyan-600 text-cyan-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
            class="px-4 py-2 border-b-2 font-medium text-sm whitespace-nowrap">
            Notes ({{ $contact->notes()->count() }})
        </button>

        <button
            @click="setTab('activities')"
            :class="tab==='activities' ? 'border-cyan-600 text-cyan-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
            class="px-4 py-2 border-b-2 font-medium text-sm whitespace-nowrap">
            Activities ({{ $contact->activities()->count() }})
        </button>

        <button
            @click="setTab('attachments')"
            :class="tab==='attachments' ? 'border-cyan-600 text-cyan-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
            class="px-4 py-2 border-b-2 font-medium text-sm whitespace-nowrap">
            Attachments ({{ $contact->attachments->count() }})
        </button>

    </div>

    <!-- Overview Tab -->
    <div x-show="tab === 'overview'">

        <!-- Contact Information -->
        <div class="bg-white rounded-lg shadow p-6">

            <h2 class="text-lg font-semibold mb-4">
                Contact Information
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <p class="text-sm text-gray-500">Full Name</p>
                    <p class="font-medium">
                        {{ $contact->Contact_Name }}
                    </p>
                </div>

            <div>
                <p class="text-sm text-gray-500">Email</p>
                @if($contact->Contact_Email)
            <a
                href="https://mail.google.com/mail/?view=cm&fs=1&to={{ urlencode($contact->Contact_Email) }}"
                target="_blank"
                class="font-medium text-blue-600 hover:text-blue-700 hover:underline">
                {{ $contact->Contact_Email }}
            </a>
                @else
                    <p class="font-medium">N/A</p>
                @endif
            </div>

                <div>
                    <p class="text-sm text-gray-500">Phone</p>

                    @if($contact->Contact_No)
                        <a
                            href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contact->Contact_No) }}"
                            target="_blank"
                            class="font-medium text-green-600 hover:text-green-700 hover:underline">
                            {{ $contact->Country_Code }} {{ $contact->Contact_No }}
                        </a>
                    @else
                        <p class="font-medium">N/A</p>
                    @endif

                </div>

            </div>

        </div>

        <!-- Company Information -->
    <div class="bg-white rounded-lg shadow p-6 mt-6">

        <h2 class="text-lg font-semibold mb-4">
            Company
        </h2>

        <div
            @if($contact->company)
                onclick="window.location='{{ route('customers.show', $contact->company->Company_ID) }}'"
            @endif
            class="flex justify-between items-center border-b py-4
                {{ $contact->company ? 'cursor-pointer hover:bg-cyan-50 transition rounded-lg px-3' : '' }}">

            <div>
                <p class="font-medium">
                    {{ $contact->company->Company_Name ?? 'Not Assigned' }}
                </p>
            </div>

        </div>

    </div>

    </div>

    <!-- Leads Tab -->
<div x-show="tab === 'leads'" x-cloak x-data="{
        addingLead: false,
        leadModalOpen: false,
        leadSearch: '',
        allLeads: @js($allOtherLeads),
        get filteredLeads() {
            return this.allLeads.filter(l =>
                this.leadSearch === '' ||
                l.name.toLowerCase().includes(this.leadSearch.toLowerCase()) ||
                (l.company ?? '').toLowerCase().includes(this.leadSearch.toLowerCase())
            );
        }
    }">
        <div class="bg-white rounded-lg shadow p-6">

            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">
                    Related Leads
                </h2>

                <div class="flex items-center gap-4">
                    @unless(auth()->user()?->isGuest())
                    <button type="button" @click="leadModalOpen = true" class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                        🔗 Link existing
                    </button>
                    <button type="button" @click="addingLead = !addingLead" class="text-cyan-600 hover:text-cyan-800 text-sm font-medium">
                        <span x-text="addingLead ? '✕ Cancel' : '+ Add new'"></span>
                    </button>
                    @endunless
                    <a href="{{ route('leads') }}" class="text-cyan-600 hover:text-cyan-700 text-sm">
                        View All Leads
                    </a>
                </div>
            </div>

            <!-- Quick add form -->
            <div x-show="addingLead" x-cloak class="mb-4 border rounded-lg p-3 bg-gray-50">
                <form method="POST" action="{{ route('contacts.addLead', $contact->Contact_ID) }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @csrf
                    <input type="text" name="Lead_Name" required placeholder="Lead Name" class="w-full border rounded-lg px-3 py-2 text-sm">
                    <input type="text" name="Source" placeholder="Source" class="w-full border rounded-lg px-3 py-2 text-sm">
                    <select name="Status" class="w-full border rounded-lg px-3 py-2 text-sm">
                        <option value="New">New</option>
                        <option value="Contacted">Contacted</option>
                        <option value="Qualified">Qualified</option>
                        <option value="Won">Won</option>
                        <option value="Lost">Lost</option>
                    </select>
                    <input type="number" step="0.01" name="Estimated_Value" placeholder="Estimated Value" class="w-full border rounded-lg px-3 py-2 text-sm">
                    <p class="md:col-span-2 text-xs text-gray-500">
                        Will be linked to {{ $contact->Contact_Name }}{{ $contact->company ? ' at ' . $contact->company->Company_Name : '' }}.
                    </p>
                    <button type="submit" class="md:col-span-2 bg-cyan-600 text-white px-4 py-2 rounded-lg hover:bg-cyan-700 text-sm">
                        Save Lead
                    </button>
                </form>
            </div>

            @if($contact->leads->count())

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">

                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">Lead Name</th>
                                <th class="px-6 py-3 text-left">Company</th>
                                <th class="px-6 py-3 text-left">Value</th>
                                <th class="px-6 py-3 text-left">Source</th>
                                <th class="px-6 py-3 text-left">Status</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">
                            @foreach($contact->leads as $lead)
                                <tr
                                    onclick="window.location='{{ route('leads.show', $lead->Lead_ID) }}'"
                                    class="cursor-pointer hover:bg-gray-50">
                                    <td class="px-6 py-4">{{ $lead->Lead_Name }}</td>
                                    <td class="px-6 py-4">{{ $lead->company->Company_Name ?? 'No Company' }}</td>
                                    <td class="px-6 py-4">{{ $lead->Estimated_Value ?? 'unknown' }}</td>
                                    <td class="px-6 py-4">{{ $lead->Source ?? 'unknown' }}</td>
                                    <td class="px-6 py-4">
                                        @if($lead->Status == 'won')
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Won</span>
                                        @elseif($lead->Status == 'New')
                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">New</span>
                                        @elseif($lead->Status == 'Qualified')
                                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">Qualified</span>
                                        @elseif($lead->Status == 'Contacted')
                                            <span class="px-2 py-1 text-xs rounded-full bg-amber-100 text-amber-700">Contacted</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Lost</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

            @else

                <div class="text-center py-8 text-gray-500 text-sm">
                    No leads linked to this contact.
                </div>

            @endif

            <!-- Floating picker modal -->
            <div x-show="leadModalOpen" x-cloak
                 @click.self="leadModalOpen = false"
                 @keydown.escape.window="leadModalOpen = false"
                 class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md max-h-[80vh] flex flex-col">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Link Existing Lead</h3>
                        <button type="button" @click="leadModalOpen = false" class="text-gray-400 hover:text-gray-600 text-lg leading-none">✕</button>
                    </div>

                    <input type="text" x-model="leadSearch" placeholder="Search by name or company..."
                           class="w-full border rounded-lg px-3 py-2 text-sm mb-3">

                    <div class="overflow-y-auto space-y-1 flex-1">
                        <template x-for="l in filteredLeads" :key="l.id">
                            <form method="POST" :action="'{{ url('contacts') }}/{{ $contact->Contact_ID }}/leads/link'">
                                @csrf
                                <input type="hidden" name="Lead_ID" :value="l.id">
                                <button type="submit"
                                        class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 text-sm flex justify-between items-center">
                                    <span class="font-medium text-gray-800" x-text="l.name"></span>
                                    <span class="text-gray-400 text-xs" x-text="l.company ?? 'No company'"></span>
                                </button>
                            </form>
                        </template>
                        <p x-show="filteredLeads.length === 0" class="text-sm text-gray-400 text-center py-4">No matches.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Notes Tab -->
    <div x-show="tab === 'notes'" x-cloak>
        @include('partials.notes', [
            'notes' => $contact->notes()->latest('Created_At')->get(),
            'ownerField' => 'Contact_ID',
            'ownerId' => $contact->Contact_ID,
        ])
    </div>

    <!-- Activities Tab -->
    <div x-show="tab === 'activities'" x-cloak>
        @include('partials.activities', [
            'activities' => $contact->activities,
            'ownerField' => 'Contact_ID',
            'ownerId' => $contact->Contact_ID,
        ])
    </div>

    <!-- Attachments Tab -->
    <div x-show="tab === 'attachments'" x-cloak>
        @include('partials.attachments', [
            'attachments' => $contact->attachments,
            'entityType' => 'Contacts',
            'entityId' => $contact->Contact_ID,
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
                    Email {{ $contact->Contact_Name }}
                </h2>
                <button @click="emailing = false" type="button" class="text-gray-400 hover:text-gray-600 text-lg leading-none">✕</button>
            </div>

            @if(!$contact->Contact_Email)
                <p class="text-sm text-red-600">This contact has no email address on file — add one before sending.</p>
            @else
                <p class="text-sm text-gray-500 mb-3">To: {{ $contact->Contact_Email }}</p>

                <form method="POST" action="{{ route('emails.send') }}" class="space-y-2">
                    @csrf
                    <input type="hidden" name="Contact_ID" value="{{ $contact->Contact_ID }}">

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