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

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Profile Card -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">

        <div class="flex items-center gap-4 mb-6">

            <div class="w-16 h-16 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xl font-bold">
                {{ strtoupper(substr($user->User_Name, 0, 1)) }}
            </div>

            <div>
                <h2 class="text-lg font-semibold">{{ $user->User_Name }}</h2>
                <p class="text-sm text-gray-500">{{ $user->User_Role }}</p>
            </div>

        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- Name -->
                <div>
                    <label class="text-sm text-gray-600">Name</label>
                    <input type="text"
                           name="User_Name"
                           value="{{ old('User_Name', $user->User_Name) }}"
                           class="w-full mt-1 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Email -->
                <div>
                    <label class="text-sm text-gray-600">Email</label>
                    <input type="email"
                           name="User_Email"
                           value="{{ old('User_Email', $user->User_Email) }}"
                           class="w-full mt-1 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Role -->
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600">Role</label>
                    <input type="text"
                           value="{{ $user->User_Role }}"
                           disabled
                           class="w-full mt-1 border rounded-lg px-3 py-2 bg-gray-100">
                </div>

            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit"
                        class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700">
                    Save Changes
                </button>
            </div>

        </form>

    </div>

    <!-- Change Password -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">

        <h2 class="text-lg font-semibold mb-4">
            Change Password
        </h2>

        <form method="POST" action="{{ route('profile.password') }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600">Current Password</label>
                    <input type="password"
                           name="current_password"
                           class="w-full mt-1 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="text-sm text-gray-600">New Password</label>
                    <input type="password"
                           name="new_password"
                           class="w-full mt-1 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="text-sm text-gray-600">Confirm New Password</label>
                    <input type="password"
                           name="new_password_confirmation"
                           class="w-full mt-1 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                </div>

            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit"
                        class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700">
                    Update Password
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

            <p>• Notification settings (coming soon)</p>
            <p>• Activity logs (coming soon)</p>

        </div>

    </div>

</div>

@endsection