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

    <div class="bg-white rounded-lg shadow p-6">

<form action="{{ route('customers.store') }}" method="POST" enctype="multipart/form-data">
@csrf

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

<div class="md:col-span-2" x-data="{ notes: [{ subject: '', content: '' }] }">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Notes (Optional)
            </label>

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

            <button
                type="button"
                @click="notes.push({ subject: '', content: '' })"
                class="text-cyan-600 hover:text-cyan-800 text-sm font-medium">
                + Add another note
            </button>
        </div>

        <!-- Attachments -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Attachments (Optional)
            </label>

            <input
                type="file"
                name="Attachments[]"
                multiple
                class="w-full border rounded-lg px-3 py-2 text-sm bg-white">

            <p class="text-xs text-gray-500 mt-1">Max 10MB each. Images, PDF, Word, Excel, or text files.</p>
        </div>

    </div>

    <div class="mt-6 flex gap-3">

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

</div>

@endsection