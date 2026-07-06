<head> 
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />


</head>

@extends('layouts.app')

@section('content')

<div class="max-w-3xl mx-auto">

    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        Profile
    </h1>

    <!-- Profile Card -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">

        <div class="flex items-center gap-4 mb-6">

            <div class="w-16 h-16 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xl font-bold">
                A
            </div>

            <div>
                <h2 class="text-lg font-semibold">Admin User</h2>
                <p class="text-sm text-gray-500">System Manager</p>
            </div>

        </div>

        <!-- Form -->
        <form>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- Name -->
                <div>
                    <label class="text-sm text-gray-600">Name</label>
                    <input type="text"
                           value="Admin User"
                           class="w-full mt-1 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Email -->
                <div>
                    <label class="text-sm text-gray-600">Email</label>
                    <input type="email"
                           value="admin@crm.com"
                           class="w-full mt-1 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Role -->
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600">Role</label>
                    <input type="text"
                           value="System Manager"
                           disabled
                           class="w-full mt-1 border rounded-lg px-3 py-2 bg-gray-100">
                </div>

            </div>

            <div class="mt-6 flex justify-end">
                <button type="button"
                        class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700">
                    Save Changes
                </button>
            </div>

        </form>

    </div>

    <!-- Extra Settings -->
    <div class="bg-white rounded-lg shadow p-6">

        <h2 class="text-lg font-semibold mb-4">
            Account Settings
        </h2>

        <div class="space-y-3 text-sm text-gray-600">

            <p>• Change password (coming soon)</p>
            <p>• Notification settings (coming soon)</p>
            <p>• Activity logs (coming soon)</p>

        </div>

    </div>

</div>

@endsection