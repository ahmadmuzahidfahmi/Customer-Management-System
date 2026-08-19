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
                {{ $customer->Company_Name }}
            </h1>

            <p class="text-gray-500">
                {{ $customer->Status ?? 'Active' }}
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
@endunless 
</div>

<!-- Quick stats strip -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">
                RM {{ number_format($customer->leads->sum('Estimated_Value'), 0) }}
            </p>
            <p class="text-xs text-gray-500">Pipeline Value</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">
                {{ $customer->leads->whereIn('Status', ['New', 'Contacted', 'Qualified'])->count() }}
            </p>
            <p class="text-xs text-gray-500">Open Leads</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">
                {{ $customer->leads->where('Status', 'Won')->count() }}
            </p>
            <p class="text-xs text-gray-500">Won Leads</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-sm font-bold text-gray-800">
                {{ $customer->Created_At?->format('M j, Y') ?? 'Unknown' }}
            </p>
            <p class="text-xs text-gray-500">Customer Since</p>
        </div>
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
            @click="setTab('contacts')"
            :class="tab==='contacts' ? 'border-cyan-600 text-cyan-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
            class="px-4 py-2 border-b-2 font-medium text-sm whitespace-nowrap">
            Contacts ({{ $customer->contacts->count() }})
        </button>

        <button
            @click="setTab('leads')"
            :class="tab==='leads' ? 'border-cyan-600 text-cyan-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
            class="px-4 py-2 border-b-2 font-medium text-sm whitespace-nowrap">
            Leads ({{ $customer->leads->count() }})
        </button>

        <button
            @click="setTab('activities')"
            :class="tab==='activities' ? 'border-cyan-600 text-cyan-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
            class="px-4 py-2 border-b-2 font-medium text-sm whitespace-nowrap">
            Activities
        </button>

        <button
            @click="setTab('notes')"
            :class="tab==='notes' ? 'border-cyan-600 text-cyan-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
            class="px-4 py-2 border-b-2 font-medium text-sm whitespace-nowrap">
            Notes ({{ $customer->notes()->count() }})
        </button>

        <button
            @click="setTab('attachments')"
            :class="tab==='attachments' ? 'border-cyan-600 text-cyan-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
            class="px-4 py-2 border-b-2 font-medium text-sm whitespace-nowrap">
            Attachments ({{ $allAttachments->count() }})
        </button>
    </div>

 <!-- Overview Tab -->
    <div x-show="tab === 'overview'">
        <div class="bg-white rounded-lg shadow p-6">

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

    @if($customer->Company_Email)

<a
    href="https://mail.google.com/mail/?view=cm&fs=1&to={{ urlencode($customer->Company_Email) }}"
    target="_blank"
    class="font-medium text-blue-600 hover:text-blue-700 hover:underline">
    {{ $customer->Company_Email }}
</a>
    @else

        <p class="font-medium">N/A</p>

    @endif
</div>

                <div>
                    <p class="text-sm text-gray-500">Phone</p>

                    @if($customer->Company_No)

                        <a
                            href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->Company_No) }}"
                            target="_blank"
                            class="font-medium text-green-600 hover:text-green-700 hover:underline">

                            {{ $customer->Country_Code }} {{ $customer->Company_No }} 

                        </a>

                    @else

                        <p class="font-medium">N/A</p>

                    @endif

                </div>

                <div>
                    <p class="text-sm text-gray-500 mb-1">Status</p>
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

<!-- Contacts Tab -->
    <div x-show="tab === 'contacts'" x-cloak x-data="{
        addingContact: false,
        contactModalOpen: false,
        contactSearch: '',
        allContacts: @js($allOtherContacts),
        get filteredContacts() {
            return this.allContacts.filter(c =>
                this.contactSearch === '' ||
                c.name.toLowerCase().includes(this.contactSearch.toLowerCase()) ||
                (c.company ?? '').toLowerCase().includes(this.contactSearch.toLowerCase())
            );
        }
    }">
        <div class="bg-white rounded-lg shadow p-6">

            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Contacts</h2>

                @unless(auth()->user()?->isGuest())
                <div class="flex gap-3">
                    <button type="button" @click="contactModalOpen = true" class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                        🔗 Link existing
                    </button>
                    <button type="button" @click="addingContact = !addingContact" class="text-cyan-600 hover:text-cyan-800 text-sm font-medium">
                        <span x-text="addingContact ? '✕ Cancel' : '+ Add new'"></span>
                    </button>
                </div>
                @endunless
            </div>

            <!-- Quick add form -->
            <div x-show="addingContact" x-cloak class="mb-4 border rounded-lg p-3 bg-gray-50">
                <form method="POST" action="{{ route('customers.addContact', $customer->Company_ID) }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @csrf
                    <input type="text" name="Contact_Name" required placeholder="Contact Name" class="w-full border rounded-lg px-3 py-2 text-sm">
                    <input type="email" name="Contact_Email" placeholder="Email" class="w-full border rounded-lg px-3 py-2 text-sm">
                    <input type="text" name="Contact_No" placeholder="Phone" class="w-full border rounded-lg px-3 py-2 text-sm">
                    <input type="text" name="Contact_Role" placeholder="Position" class="w-full border rounded-lg px-3 py-2 text-sm">
                    <button type="submit" class="md:col-span-2 bg-cyan-600 text-white px-4 py-2 rounded-lg hover:bg-cyan-700 text-sm">
                        Save Contact
                    </button>
                </form>
            </div>

@forelse($customer->contacts as $contact)

                <div
                    onclick="window.location='{{ route('contacts.show', $contact->Contact_ID) }}'"
class="bg-white border rounded-lg shadow-sm hover:shadow-md hover:bg-cyan-50 transition p-4 mb-3 cursor-pointer">

                    <div class="flex justify-between items-center">

                        <div>
                            <p class="font-medium text-gray-800">
                                {{ $contact->Contact_Name }}
                            </p>

                            <p class="text-sm text-gray-500">
                                {{ $contact->Contact_Role ?? 'No Position' }}
                            </p>
                        </div>

                    </div>

                    <div class="mt-2 text-sm text-gray-600">

                <!-- Copy to Clipboard Feature -->
                <div
                    x-data="{ copied: false }"
                    class="flex items-center gap-2">

                <div class="group flex items-center gap-2">

                    <span>{{ $contact->Contact_Email }}</span>

                    <button
                        @click.stop="
                            navigator.clipboard.writeText('{{ $contact->Contact_Email }}');
                            copied = true;
                            setTimeout(() => copied = false, 2000);
                                "
                            class="opacity-0 group-hover:opacity-100 transition
                            text-gray-400 hover:text-cyan-600">
                            📋
                    </button>
                </div>

                    <span
                        x-show="copied"
                        x-transition
                        class="text-green-600 text-xs">
                        Copied!
                    </span>
                    
                </div>

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

            @empty

                <div class="text-gray-500 text-sm py-4">
                    No contacts found for this company.
                </div>

@endforelse

            <!-- Floating picker modal -->
            <div x-show="contactModalOpen" x-cloak
                 @click.self="contactModalOpen = false"
                 @keydown.escape.window="contactModalOpen = false"
                 class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md max-h-[80vh] flex flex-col">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Link Existing Contact</h3>
                        <button type="button" @click="contactModalOpen = false" class="text-gray-400 hover:text-gray-600 text-lg leading-none">✕</button>
                    </div>

                    <input type="text" x-model="contactSearch" placeholder="Search by name or company..."
                           class="w-full border rounded-lg px-3 py-2 text-sm mb-3">

                    <div class="overflow-y-auto space-y-1 flex-1">
                        <template x-for="c in filteredContacts" :key="c.id">
                            <form method="POST" :action="'{{ url('customers') }}/{{ $customer->Company_ID }}/contacts/link'">
                                @csrf
                                <input type="hidden" name="Contact_ID" :value="c.id">
                                <button type="submit"
                                        class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 text-sm flex justify-between items-center">
                                    <span class="font-medium text-gray-800" x-text="c.name"></span>
                                    <span class="text-gray-400 text-xs" x-text="c.company ?? 'No company'"></span>
                                </button>
                            </form>
                        </template>
                        <p x-show="filteredContacts.length === 0" class="text-sm text-gray-400 text-center py-4">No matches.</p>
                    </div>
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
                </div>
            </div>

            <!-- Quick add form -->
            <div x-show="addingLead" x-cloak class="mb-4 border rounded-lg p-3 bg-gray-50">
                <form method="POST" action="{{ route('customers.addLead', $customer->Company_ID) }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
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
                    <button type="submit" class="md:col-span-2 bg-cyan-600 text-white px-4 py-2 rounded-lg hover:bg-cyan-700 text-sm">
                        Save Lead
                    </button>
                </form>
            </div>

            @if($customer->leads->count())

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">

                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">Lead Name</th>
                                <th class="px-6 py-3 text-left">Value</th>
                                <th class="px-6 py-3 text-left">Source</th>
                                <th class="px-6 py-3 text-left">Last Update</th>
                                <th class="px-6 py-3 text-left">Status</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">
                            @foreach($customer->leads as $lead)
                                <tr
                                    onclick="window.location='{{ route('leads.show', $lead->Lead_ID) }}'"
                                    class="cursor-pointer hover:bg-cyan-50">
                                    <td class="px-6 py-4">{{ $lead->Lead_Name }}</td>
                                    <td class="px-6 py-4">{{ $lead->Estimated_Value ?? 'unknown' }}</td>
                                    <td class="px-6 py-4">{{ $lead->Source ?? 'unknown' }}</td>
                                    <td class="px-6 py-4">{{ $lead->Updated_At ?? 'unknown' }}</td>
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

                <div class="text-gray-500 text-sm py-4">
                    No leads linked to this customer.
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
                            <form method="POST" :action="'{{ url('customers') }}/{{ $customer->Company_ID }}/leads/link'">
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

    <!-- Activities Tab -->
    <div x-show="tab === 'activities'" x-cloak>
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">
                Activity Cards
            </h2>

            <div class="text-center py-8 text-gray-500 text-sm">
                Activity tracking for this customer is coming soon.
            </div>
        </div>
    </div>

    <!-- Notes Tab -->
    <div x-show="tab === 'notes'" x-cloak>
        @include('partials.notes', [
            'notes' => $customer->notes()->latest('Created_At')->get(),
            'ownerField' => 'Company_ID',
            'ownerId' => $customer->Company_ID,
        ])
    </div>

<!-- Attachments Tab -->
    <div x-show="tab === 'attachments'" x-cloak x-data="{
        addingFile: false,
        selected: [],
        allIds: {{ Js::from($allAttachments->pluck('Attachment_ID')) }},
        toggleAll() {
            this.selected = this.selected.length === this.allIds.length ? [] : [...this.allIds];
        }
    }">

        <form id="bulk-attachment-form" method="POST" action="{{ route('attachments.bulkDestroy') }}">
            @csrf
            @method('DELETE')
            <input type="hidden" name="delete_backup" value="0" x-ref="bulkDeleteBackupInput">
        </form>

        <div class="bg-white rounded-lg shadow p-4 mb-4">
            <div class="flex justify-between items-center mb-3">
                <h2 class="text-lg font-semibold text-gray-800">Attachments</h2>

                @unless(auth()->user()?->isGuest())
                <button
                    @click="addingFile = !addingFile"
                    type="button"
                    class="w-8 h-8 flex items-center justify-center rounded-full bg-cyan-600 text-white hover:bg-cyan-700 text-lg leading-none">
                    <span x-text="addingFile ? '✕' : '+'"></span>
                </button>
                @endunless
            </div>

            <div x-show="addingFile" x-cloak class="space-y-2 mb-2 border rounded-lg p-3 bg-gray-50">
                <form method="POST" action="{{ route('attachments.store') }}" enctype="multipart/form-data" class="space-y-2">
                    @csrf
                    <input type="hidden" name="Entity_Type" value="Company">
                    <input type="hidden" name="Entity_ID" value="{{ $customer->Company_ID }}">

                    <input type="file" name="file[]" multiple required class="w-full border rounded-lg px-3 py-2 text-sm bg-white">
                    <p class="text-xs text-gray-500">Max 10MB each. Uploaded here attaches directly to {{ $customer->Company_Name }}.</p>

                    <button type="submit" class="bg-cyan-600 text-white px-4 py-2 rounded-lg hover:bg-cyan-700 text-sm">Upload</button>
                </form>
            </div>

            @if($allAttachments->count() > 0)
                @unless(auth()->user()?->isGuest())
                <div class="flex items-center justify-between pt-2 border-t">
                    <label class="flex items-center gap-2 text-sm text-gray-600">
                        <input type="checkbox" :checked="selected.length === allIds.length" @change="toggleAll()">
                        Select all ({{ $allAttachments->count() }} total)
                    </label>

                    <button
                        type="button"
                        x-show="selected.length > 0"
                        x-cloak
                        @click="
                            if (!confirm(`Delete ${selected.length} selected file(s)?`)) return;
                            const deleteBackup = confirm('Also delete the backup copies in Google Drive?');
                            $refs.bulkDeleteBackupInput.value = deleteBackup ? '1' : '0';
                            document.getElementById('bulk-attachment-form').submit();
                        "
                        class="bg-red-600 text-white px-3 py-1.5 rounded-lg hover:bg-red-700 text-sm">
                        Delete Selected (<span x-text="selected.length"></span>)
                    </button>
                </div>
                @endunless
            @endif
        </div>

        @php
            $companyFiles = $allAttachments->where('Entity_Type', 'Company');
            $contactFiles = $allAttachments->where('Entity_Type', 'Contacts')->groupBy('Entity_ID');
            $leadFiles = $allAttachments->where('Entity_Type', 'Leads')->groupBy('Entity_ID');
        @endphp

        @if($companyFiles->count())
            <div class="bg-white rounded-lg shadow p-4 mb-4">
                <h3 class="font-semibold text-gray-700 mb-3">Company Files ({{ $companyFiles->count() }})</h3>
                <div class="space-y-2">
                    @foreach($companyFiles as $attachment)
                        @include('partials._attachment-row', ['attachment' => $attachment])
                    @endforeach
                </div>
            </div>
        @endif

        @if($contactFiles->count())
            <div class="bg-white rounded-lg shadow p-4 mb-4">
                <h3 class="font-semibold text-gray-700 mb-3">Contact Files</h3>
                @foreach($contactFiles as $contactId => $files)
                    <div class="mb-4 last:mb-0">
                        <a href="{{ route('contacts.show', $contactId) }}" class="text-sm font-medium text-cyan-700 hover:underline">
                            {{ $files->first()->entityLabel() }}
                        </a>
                        <span class="text-xs text-gray-400">({{ $files->count() }})</span>

                        <div class="space-y-2 mt-2 pl-3 border-l-2 border-gray-100">
                            @foreach($files as $attachment)
                                @include('partials._attachment-row', ['attachment' => $attachment])
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if($leadFiles->count())
            <div class="bg-white rounded-lg shadow p-4 mb-4">
                <h3 class="font-semibold text-gray-700 mb-3">Lead Files</h3>
                @foreach($leadFiles as $leadId => $files)
                    <div class="mb-4 last:mb-0">
                        <a href="{{ route('leads.show', $leadId) }}" class="text-sm font-medium text-cyan-700 hover:underline">
                            {{ $files->first()->entityLabel() }}
                        </a>
                        <span class="text-xs text-gray-400">({{ $files->count() }})</span>

                        <div class="space-y-2 mt-2 pl-3 border-l-2 border-gray-100">
                            @foreach($files as $attachment)
                                @include('partials._attachment-row', ['attachment' => $attachment])
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if($allAttachments->isEmpty())
            <div class="bg-white rounded-lg shadow p-4 text-sm text-gray-500">No attachments yet.</div>
        @endif

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
                    Email {{ $customer->Company_Name }}
                </h2>

                <button
                    @click="emailing = false"
                    type="button"
                    class="text-gray-400 hover:text-gray-600 text-lg leading-none">
                    ✕
                </button>
            </div>

            @if(!$customer->Company_Email)

                <p class="text-sm text-red-600">
                    This customer has no email address on file.
                </p>

            @else

                <p class="text-sm text-gray-500 mb-3">
                    To: {{ $customer->Company_Email }}
                </p>

                <form method="POST" action="{{ route('emails.send') }}" class="space-y-2">
                    @csrf

                    <input
                        type="hidden"
                        name="Company_ID"
                        value="{{ $customer->Company_ID }}">

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

                        <button
                            type="button"
                            @click="emailing = false"
                            class="px-4 py-2 rounded-lg bg-gray-200 text-sm">
                            Cancel
                        </button>

                        <button
                            type="submit"
                            class="px-4 py-2 rounded-lg bg-cyan-600 text-white text-sm hover:bg-cyan-700">
                            Send Email
                        </button>

                    </div>
                </form>

            @endif

        </div>

    </div>

</div>

@endsection