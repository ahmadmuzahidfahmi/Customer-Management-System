@extends('layouts.app')

@section('content')
<head> 
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />


</head>

<h1 class="text-2xl font-bold mb-6">
    Add New Lead
</h1>

<div class="bg-white rounded-lg shadow p-6">

<form action="{{ route('leads.store') }}"
      method="POST">

    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Lead Name -->
        <div>
            <label class="block text-sm font-medium mb-1">
                Lead Name
            </label>

            <input
                type="text"
                name="Lead_Name"
                required
                class="w-full border rounded-lg px-3 py-2">
        </div>

<!-- Company & Contact -->
        <div x-data="{
            selectedCompany: '{{ old('Company_ID', '') }}',
            selectedContact: '{{ old('Contact_ID', '') }}',
            companyOpen: false,
            companySearch: '',
            contactOpen: false,
            contactSearch: '',
            companies: @js($customers->map(fn ($c) => [
                'id' => $c->Company_ID,
                'name' => $c->Company_Name,
                'contacts' => $c->contacts->map(fn ($ct) => ['id' => $ct->Contact_ID, 'name' => $ct->Contact_Name]),
            ])),
            get contactsForCompany() {
                const company = this.companies.find(c => c.id == this.selectedCompany);
                return company ? company.contacts : [];
            },
            get filteredCompanies() {
                if (!this.companySearch) return this.companies;
                const q = this.companySearch.toLowerCase();
                return this.companies.filter(c => c.name.toLowerCase().includes(q));
            },
            get filteredContacts() {
                if (!this.contactSearch) return this.contactsForCompany;
                const q = this.contactSearch.toLowerCase();
                return this.contactsForCompany.filter(c => c.name.toLowerCase().includes(q));
            },
            get selectedCompanyName() {
                return this.companies.find(c => c.id == this.selectedCompany)?.name || '';
            },
            get selectedContactName() {
                return this.contactsForCompany.find(c => c.id == this.selectedContact)?.name || '';
            },
            onContactChange() {
                if (!this.selectedContact) return;
                for (const c of this.companies) {
                    if (c.contacts.some(ct => ct.id == this.selectedContact)) {
                        this.selectedCompany = c.id;
                        break;
                    }
                }
            },
            onCompanyChange() {
                if (!this.contactsForCompany.some(ct => ct.id == this.selectedContact)) {
                    this.selectedContact = '';
                }
            },
            pickCompany(c) {
                this.selectedCompany = c.id;
                this.onCompanyChange();
                this.companyOpen = false;
                this.companySearch = '';
            },
            pickContact(c) {
                this.selectedContact = c.id;
                this.onContactChange();
                this.contactOpen = false;
                this.contactSearch = '';
            }
        }" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <label class="block text-sm font-medium mb-1">Company</label>

                <input type="hidden" name="Company_ID" x-model="selectedCompany">

                <button type="button" @click="companyOpen = true; $nextTick(() => $refs.companySearch.focus())"
                        class="w-full border rounded-lg px-3 py-2 text-sm text-left bg-white hover:bg-gray-50">
                    <span :class="selectedCompanyName ? 'text-gray-800' : 'text-gray-400'"
                          x-text="selectedCompanyName || 'Select Company'"></span>
                </button>

                <div x-show="companyOpen" x-cloak
                     @click.self="companyOpen = false"
                     @keydown.escape.window="companyOpen = false"
                     class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
                    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md max-h-[80vh] flex flex-col">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Select a Company</h3>
                            <button type="button" @click="companyOpen = false" class="text-gray-400 hover:text-gray-600 text-lg leading-none">✕</button>
                        </div>

                        <input type="text" x-model="companySearch" x-ref="companySearch" placeholder="Search..."
                               class="w-full border rounded-lg px-3 py-2 text-sm mb-3">

                        <div class="overflow-y-auto space-y-1 flex-1">
                            <template x-for="c in filteredCompanies" :key="c.id">
                                <button type="button" @click="pickCompany(c)"
                                        class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 text-sm font-medium text-gray-800" x-text="c.name"></button>
                            </template>
                            <p x-show="filteredCompanies.length === 0" class="text-sm text-gray-400 text-center py-4">No matches.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Contact (Optional)</label>

                <input type="hidden" name="Contact_ID" x-model="selectedContact">

                <button type="button"
                        @click="contactOpen = true; $nextTick(() => $refs.contactSearch.focus())"
                        class="w-full border rounded-lg px-3 py-2 text-sm text-left bg-white hover:bg-gray-50">
                    <span :class="selectedContactName ? 'text-gray-800' : 'text-gray-400'"
                          x-text="selectedContactName || 'No specific contact'"></span>
                </button>

                <p class="text-xs text-gray-400 mt-1" x-show="contactsForCompany.length === 0 && selectedCompany">
                    This company has no contacts yet.
                </p>

                <div x-show="contactOpen" x-cloak
                     @click.self="contactOpen = false"
                     @keydown.escape.window="contactOpen = false"
                     class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
                    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md max-h-[80vh] flex flex-col">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">Select a Contact</h3>
                            <button type="button" @click="contactOpen = false" class="text-gray-400 hover:text-gray-600 text-lg leading-none">✕</button>
                        </div>

                        <input type="text" x-model="contactSearch" x-ref="contactSearch" placeholder="Search..."
                               class="w-full border rounded-lg px-3 py-2 text-sm mb-3">

                        <div class="overflow-y-auto space-y-1 flex-1">
                            <template x-for="c in filteredContacts" :key="c.id">
                                <button type="button" @click="pickContact(c)"
                                        class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 text-sm font-medium text-gray-800" x-text="c.name"></button>
                            </template>
                            <p x-show="filteredContacts.length === 0" class="text-sm text-gray-400 text-center py-4">
                                No matches<span x-show="!selectedCompany"> — select a company first</span>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Source -->
        <div>
            <label class="block text-sm font-medium mb-1">
                Source
            </label>

            <select
                name="Source"
                class="w-full border rounded-lg px-3 py-2">

                <option value="Website">Website</option>
                <option value="Referral">Referral</option>
                <option value="Email">Email</option>
                <option value="Phone Call">Phone Call</option>
                <option value="Walk In">Walk In</option>

            </select>
        </div>

        <!-- Assigned User -->
        <div>
            <label class="block text-sm font-medium mb-1">
                Assigned To
            </label>

            @include('partials.entity-picker', [
                'fieldName' => 'User_ID',
                'options' => $users->map(fn ($u) => ['id' => $u->User_ID, 'label' => $u->User_Name]),
                'placeholder' => 'Unassigned',
                'title' => 'Assign To',
                'selectedId' => old('User_ID'),
            ])
        </div>

        <!-- Status -->
        <div>
            <label class="block text-sm font-medium mb-1">
                Status
            </label>

            <select
                name="Status"
                class="w-full border rounded-lg px-3 py-2">

                <option value="New">New</option>
                <option value="Contacted">Contacted</option>
                <option value="Qualified">Qualified</option>
                <option value="Won">Won</option>
                <option value="Lost">Lost</option>

            </select>
        </div>

        <!-- Estimated Value -->
                <div>
            <label class="block text-sm font-medium mb-1">
                Estimated Value
            </label>

            <input
                type="number"
                name="Estimated_Value"
                step="1"
                class="w-full border rounded-lg px-3 py-2">
        </div>


        <!-- Note -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-1">
                Note
            </label>

            <textarea
                name="Lead_Note"
                rows="4"
                class="w-full border rounded-lg px-3 py-2"></textarea>
        </div>

    </div>

    <div class="mt-6 flex gap-3">

        <button
            type="submit"
            class="px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700">

            Save Lead

        </button>

        <a href="{{ route('leads') }}"
           class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">

            Cancel

        </a>

    </div>

</form>

</div>

@endsection