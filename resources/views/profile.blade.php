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

        <!-- My Work -->

        <h3 class="text-lg font-semibold mb-4">My Work</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- My Leads -->
            <div>
                <div class="flex justify-between items-center mb-3">
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Assigned Leads</p>
                    <a href="{{ route('leads') }}" class="text-xs text-cyan-600 hover:underline">View All</a>
                </div>

                <div class="space-y-2">
                    @forelse($myLeads as $lead)
                        <a href="{{ route('leads.show', $lead->Lead_ID) }}"
                           class="block border rounded-lg p-3 hover:bg-cyan-50 hover:shadow-sm transition">
                            <p class="font-medium text-gray-800 text-sm">{{ $lead->Lead_Name }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $lead->company->Company_Name ?? 'No Company' }} ·
                                <span class="font-medium">{{ $lead->Status }}</span>
                            </p>
                        </a>
                    @empty
                        <p class="text-sm text-gray-400">No active leads assigned to you.</p>
                    @endforelse
                </div>
            </div>

            <!-- My Pending Activities -->
            <div>
                <div class="flex justify-between items-center mb-3">
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Pending Activities</p>
                    <a href="{{ route('activities.index') }}" class="text-xs text-cyan-600 hover:underline">View All</a>
                </div>

                <div class="space-y-2">
                    @forelse($myPendingActivities as $activity)
                        @php
                            $borderColor = $activity->isOverdue()
                                ? 'border-red-500'
                                : ($activity->Dead_Line && $activity->Dead_Line->isToday() ? 'border-yellow-500' : 'border-blue-500');
                        @endphp
                        <div class="border-l-4 {{ $borderColor }} pl-3 py-1">
                            <p class="font-medium text-gray-800 text-sm">{{ $activity->Subject }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $activity->lead->Lead_Name ?? $activity->contact->Contact_Name ?? 'Unlinked' }}
                                @if($activity->Dead_Line)
                                    — {{ $activity->Dead_Line->isToday() ? 'Due Today' : $activity->Dead_Line->format('d M') }}
                                @endif
                            </p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">No pending activities assigned to you.</p>
                    @endforelse
                </div>
            </div>

        </div>


        <div class="space-y-3 text-sm text-gray-600">

            <p>• Notification settings (coming soon)</p>
            <p>• Activity logs (coming soon)</p>

        </div>

    </div>

</div>

@endsection