<head> 
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

@extends('layouts.app')

@section('content')

<div class="flex items-center gap-2 mb-6">
    <h1 class="text-3xl font-bold text-gray-800">
        Customers
    </h1>

    <span class="text-lg text-gray-500">
        / Add New Customer
    </span>
</div>

<form action="{{ route('customers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
@csrf

    <!-- Company Details -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Company Details</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Company Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Company Name
                </label>

                <input
                    type="text"
                    name="Company_Name"
                    required
                    class="w-full border rounded-lg px-3 py-2">
            </div>

            <!-- Company Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Company Email
                </label>

                <input
                    type="email"
                    name="Company_Email"
                    class="w-full border rounded-lg px-3 py-2">
            </div>

            <!-- Phone Number -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Phone Number
                </label>

                <div class="flex gap-2">

                <div
                    x-data="{
                        open:false,
                        selected:'{{ old('Country_Code', '+60') }}',
                        countries:@js($countries),

                        get selectedCountry(){

                            return this.countries.find(
                                c => c.code === this.selected
                            );

                        },

                        selectCountry(country){

                            this.selected = country.code;
                            this.open = false;

                        }

                    }"

                    class="relative w-40"
                >


                <input
                    type="hidden"
                    name="Country_Code"
                    x-model="selected"
                >


                <!-- Selected value -->
                <button
                    type="button"
                    @click="open=!open"

                    class="
                    w-full
                    border
                    rounded-lg
                    px-3
                    py-2
                    text-left
                    bg-white
                    "
                >

                    <span x-text="selected"></span>

                </button>



                <!-- Dropdown -->
                <div
                    x-show="open"
                    @click.outside="open=false"

                    class="
                    absolute
                    z-50
                    w-full
                    bg-white
                    border
                    rounded-lg
                    shadow-lg
                    mt-1
                    max-h-60
                    overflow-y-auto
                    "
                >


                <template x-for="country in countries"
                :key="country.code">


                <div

                @click="selectCountry(country)"

                class="
                px-3
                py-2
                cursor-pointer
                hover:bg-gray-100
                "
                >

                <span x-text="country.name"></span>

                <span class="text-gray-500">
                (
                <span x-text="country.code"></span>
                )
                </span>


                </div>


                </template>


                </div>


                </div>

                    <input
                        type="text"
                        name="Company_No"
                        placeholder="123456789"
                        class="flex-1 border rounded-lg px-3 py-2">

                </div>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Status
                </label>

                <select
                    name="Status"
                    class="w-full border rounded-lg px-3 py-2">

                    <option value="Active">Active</option>
                    <option value="Lead">Lead</option>
                    <option value="Inactive">Inactive</option>

                </select>
            </div>

        </div>
    </div>

    <!-- Notes -->
    <div class="bg-white rounded-lg shadow p-6" x-data="{ notes: [{ subject: '', content: '' }] }">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Notes (Optional)</h2>
            <button
                type="button"
                @click="notes.push({ subject: '', content: '' })"
                class="text-cyan-600 hover:text-cyan-800 text-sm font-medium">
                + Add another note
            </button>
        </div>

        <template x-for="(note, index) in notes" :key="index">
            <div class="space-y-2 mb-3 border rounded-lg p-3 bg-gray-50">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-400" x-text="'Note ' + (index + 1)"></span>
                    <button
                        type="button"
                        x-show="notes.length > 1"
                        @click="notes.splice(index, 1)"
                        class="text-red-500 hover:text-red-700 text-xs">
                        Remove
                    </button>
                </div>

                <input
                    type="text"
                    :name="`Notes[${index}][Subject]`"
                    x-model="note.subject"
                    placeholder="Subject (optional)"
                    class="w-full border rounded-lg px-3 py-2 text-sm">

                <textarea
                    :name="`Notes[${index}][Content]`"
                    x-model="note.content"
                    rows="3"
                    placeholder="Write a note..."
                    class="w-full border rounded-lg px-3 py-2 text-sm"></textarea>
            </div>
        </template>
    </div>

    <!-- Attachments -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Attachments (Optional)</h2>

        <input
            type="file"
            name="Attachments[]"
            multiple
            class="w-full border rounded-lg px-3 py-2 text-sm bg-white">

        <p class="text-xs text-gray-500 mt-1">Max 10MB each. Images, PDF, Word, Excel, or text files.</p>
    </div>

    <!-- Contacts -->
    <div class="bg-white rounded-lg shadow p-6" x-data="{
        contacts: [],
        linkedContacts: [],
        contactModalOpen: false,
        contactSearch: '',
        allContacts: @js($existingContacts),
        get filteredContacts() {
            return this.allContacts.filter(c =>
                !this.linkedContacts.some(l => l.id === c.id) &&
                (this.contactSearch === '' ||
                    c.name.toLowerCase().includes(this.contactSearch.toLowerCase()) ||
                    (c.company ?? '').toLowerCase().includes(this.contactSearch.toLowerCase()))
            );
        },
        linkContact(c) {
            this.linkedContacts.push(c);
            this.contactModalOpen = false;
            this.contactSearch = '';
        }
    }">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Contacts (Optional)</h2>
            <div class="flex gap-3">
                <button type="button" @click="contactModalOpen = true" class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                    🔗 Link existing
                </button>
                <button type="button" @click="contacts.push({ name: '', email: '', countryCode: '+60', phone: '', role: '' })"
                        class="text-cyan-600 hover:text-cyan-800 text-sm font-medium">
                    + Add new
                </button>
            </div>
        </div>

        <!-- Linked existing contacts -->
        <template x-for="(c, index) in linkedContacts" :key="'linked-' + c.id">
            <div class="flex items-center justify-between border rounded-lg p-3 mb-2 bg-cyan-50">
                <div>
                    <p class="text-sm font-medium text-gray-800" x-text="c.name"></p>
                    <p class="text-xs text-gray-500" x-text="'Currently at: ' + (c.company ?? 'No company')"></p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-amber-600">Will move to this company</span>
                    <button type="button" @click="linkedContacts.splice(index, 1)" class="text-red-500 hover:text-red-700 text-xs">Remove</button>
                </div>
                <input type="hidden" name="Existing_Contacts[]" :value="c.id">
            </div>
        </template>

        <!-- New contact rows -->
        <template x-for="(contact, index) in contacts" :key="index">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3 border rounded-lg p-3 bg-gray-50">
                <div class="md:col-span-2 flex justify-between items-center">
                    <span class="text-xs text-gray-400" x-text="'New Contact ' + (index + 1)"></span>
                    <button type="button" @click="contacts.splice(index, 1)" class="text-red-500 hover:text-red-700 text-xs">Remove</button>
                </div>

                <input type="text" :name="`Contacts[${index}][Contact_Name]`" x-model="contact.name"
                       placeholder="Contact Name" class="w-full border rounded-lg px-3 py-2 text-sm">

                <input type="email" :name="`Contacts[${index}][Contact_Email]`" x-model="contact.email"
                       placeholder="Email" class="w-full border rounded-lg px-3 py-2 text-sm">

                <div class="flex gap-2">
                    <select :name="`Contacts[${index}][Country_Code]`" x-model="contact.countryCode"
                            class="border rounded-lg px-2 py-2 text-sm w-28">
                        @foreach($countries as $country)
                            <option value="{{ $country['code'] }}">{{ $country['code'] }}</option>
                        @endforeach
                    </select>
                    <input type="text" :name="`Contacts[${index}][Contact_No]`" x-model="contact.phone"
                           placeholder="Phone" class="flex-1 border rounded-lg px-3 py-2 text-sm">
                </div>

                <input type="text" :name="`Contacts[${index}][Contact_Role]`" x-model="contact.role"
                       placeholder="Position (Manager, CEO...)" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
        </template>

        <p x-show="contacts.length === 0 && linkedContacts.length === 0" class="text-sm text-gray-400">No contacts added yet.</p>

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
                        <button type="button" @click="linkContact(c)"
                                class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 text-sm flex justify-between items-center">
                            <span class="font-medium text-gray-800" x-text="c.name"></span>
                            <span class="text-gray-400 text-xs" x-text="c.company ?? 'No company'"></span>
                        </button>
                    </template>
                    <p x-show="filteredContacts.length === 0" class="text-sm text-gray-400 text-center py-4">No matches.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Leads -->
    <div class="bg-white rounded-lg shadow p-6" x-data="{
        leads: [],
        linkedLeads: [],
        leadModalOpen: false,
        leadSearch: '',
        allLeads: @js($existingLeads),
        get filteredLeads() {
            return this.allLeads.filter(l =>
                !this.linkedLeads.some(x => x.id === l.id) &&
                (this.leadSearch === '' ||
                    l.name.toLowerCase().includes(this.leadSearch.toLowerCase()) ||
                    (l.company ?? '').toLowerCase().includes(this.leadSearch.toLowerCase()))
            );
        },
        linkLead(l) {
            this.linkedLeads.push(l);
            this.leadModalOpen = false;
            this.leadSearch = '';
        }
    }">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Leads (Optional)</h2>
            <div class="flex gap-3">
                <button type="button" @click="leadModalOpen = true" class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                    🔗 Link existing
                </button>
                <button type="button" @click="leads.push({ name: '', source: '', status: 'New', value: '' })"
                        class="text-cyan-600 hover:text-cyan-800 text-sm font-medium">
                    + Add new
                </button>
            </div>
        </div>

        <!-- Linked existing leads -->
        <template x-for="(l, index) in linkedLeads" :key="'linked-' + l.id">
            <div class="flex items-center justify-between border rounded-lg p-3 mb-2 bg-cyan-50">
                <div>
                    <p class="text-sm font-medium text-gray-800" x-text="l.name"></p>
                    <p class="text-xs text-gray-500" x-text="'Currently at: ' + (l.company ?? 'No company')"></p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-amber-600">Will move to this company</span>
                    <button type="button" @click="linkedLeads.splice(index, 1)" class="text-red-500 hover:text-red-700 text-xs">Remove</button>
                </div>
                <input type="hidden" name="Existing_Leads[]" :value="l.id">
            </div>
        </template>

        <!-- New lead rows -->
        <template x-for="(lead, index) in leads" :key="index">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3 border rounded-lg p-3 bg-gray-50">
                <div class="md:col-span-2 flex justify-between items-center">
                    <span class="text-xs text-gray-400" x-text="'New Lead ' + (index + 1)"></span>
                    <button type="button" @click="leads.splice(index, 1)" class="text-red-500 hover:text-red-700 text-xs">Remove</button>
                </div>

                <input type="text" :name="`Leads[${index}][Lead_Name]`" x-model="lead.name"
                       placeholder="Lead Name" class="w-full border rounded-lg px-3 py-2 text-sm">

                <input type="text" :name="`Leads[${index}][Source]`" x-model="lead.source"
                       placeholder="Source (Referral, Website...)" class="w-full border rounded-lg px-3 py-2 text-sm">

                <select :name="`Leads[${index}][Status]`" x-model="lead.status"
                        class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="New">New</option>
                    <option value="Contacted">Contacted</option>
                    <option value="Qualified">Qualified</option>
                    <option value="Won">Won</option>
                    <option value="Lost">Lost</option>
                </select>

                <input type="number" step="0.01" :name="`Leads[${index}][Estimated_Value]`" x-model="lead.value"
                       placeholder="Estimated Value" class="w-full border rounded-lg px-3 py-2 text-sm">
            </div>
        </template>

        <p x-show="leads.length === 0 && linkedLeads.length === 0" class="text-sm text-gray-400">No leads added yet.</p>

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
                        <button type="button" @click="linkLead(l)"
                                class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 text-sm flex justify-between items-center">
                            <span class="font-medium text-gray-800" x-text="l.name"></span>
                            <span class="text-gray-400 text-xs" x-text="l.company ?? 'No company'"></span>
                        </button>
                    </template>
                    <p x-show="filteredLeads.length === 0" class="text-sm text-gray-400 text-center py-4">No matches.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex gap-3">

        <button
            type="submit"
            class="px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700">
            Save Customer
        </button>

        <a href="{{ route('customers') }}"
           class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
            Back
        </a>

    </div>

</form>

@endsection