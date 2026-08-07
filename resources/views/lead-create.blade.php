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
            companies: @js($customers->map(fn ($c) => [
                'id' => $c->Company_ID,
                'name' => $c->Company_Name,
                'contacts' => $c->contacts->map(fn ($ct) => ['id' => $ct->Contact_ID, 'name' => $ct->Contact_Name]),
            ])),
            get contactsForCompany() {
                const company = this.companies.find(c => c.id == this.selectedCompany);
                return company ? company.contacts : [];
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
            }
        }" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <label class="block text-sm font-medium mb-1">Company</label>
                <select name="Company_ID" x-model="selectedCompany" @change="onCompanyChange()" class="w-full border rounded-lg px-3 py-2">
                    <option value="">Select Company</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->Company_ID }}">{{ $customer->Company_Name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Contact (Optional)</label>
                <select name="Contact_ID" x-model="selectedContact" @change="onContactChange()" class="w-full border rounded-lg px-3 py-2">
                    <option value="">No specific contact</option>
                    <template x-for="c in contactsForCompany" :key="c.id">
                        <option :value="c.id" x-text="c.name"></option>
                    </template>
                </select>
                <p class="text-xs text-gray-400 mt-1" x-show="contactsForCompany.length === 0 && selectedCompany">
                    This company has no contacts yet.
                </p>
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

            <select
                name="User_ID"
                class="w-full border rounded-lg px-3 py-2">

                <option value="">
                    Unassigned
                </option>

                @foreach($users as $user)
                    <option value="{{ $user->User_ID }}">
                        {{ $user->User_Name }}
                    </option>
                @endforeach

            </select>
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
                step="10"
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