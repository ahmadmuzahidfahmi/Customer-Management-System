<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

@extends('layouts.app')

@section('content')

<div class="max-w-5xl mx-auto" x-data="{ tab: 'profile' }">

    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        Profile
    </h1>

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Tabs -->
    <div class="flex gap-2 border-b border-gray-200 mb-6">
        <button
            type="button"
            @click="tab = 'profile'"
            :class="tab === 'profile' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
            class="px-4 py-2 text-sm font-medium border-b-2 transition-colors">
            My Profile
        </button>

        @if($isAdmin)
        <button
            type="button"
            @click="tab = 'users'"
            :class="tab === 'users' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
            class="px-4 py-2 text-sm font-medium border-b-2 transition-colors">
            Manage Users
        </button>
        @endif
    </div>

    <!-- ============================= -->
    <!-- Tab: My Profile (unchanged)   -->
    <!-- ============================= -->
    <div x-show="tab === 'profile'" x-cloak class="max-w-3xl">

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
        </div>

    </div>

    <!-- ============================= -->
    <!-- Tab: Manage Users (admin only) -->
    <!-- ============================= -->
    @if($isAdmin)
    <div x-show="tab === 'users'" x-cloak x-data="{ editingId: null, showCreate: false }">

        <div class="bg-white rounded-lg shadow p-6">

            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">All Users</h2>
                <button
                    type="button"
                    @click="showCreate = true"
                    class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-indigo-700">
                    + New User
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2 pr-4">Name</th>
                            <th class="py-2 pr-4 hidden sm:table-cell">Email</th>
                            <th class="py-2 pr-4">Role</th>
                            <th class="py-2 pr-4 hidden sm:table-cell">Status</th>
                            <th class="py-2 pr-4 hidden md:table-cell">Last Login</th>
                            <th class="py-2 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $u)
                            <!-- Display row -->
                            <tr x-show="editingId !== {{ $u->User_ID }}" class="border-b last:border-0">
                                <td class="py-3 pr-4 font-medium text-gray-800">
                                    {{ $u->User_Name }}
                                    @if($u->User_ID === $user->User_ID)
                                        <span class="text-xs text-gray-400">(you)</span>
                                    @endif
                                </td>
                                <td class="py-3 pr-4 hidden sm:table-cell text-gray-600">{{ $u->User_Email }}</td>
                                <td class="py-3 pr-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                        {{ $u->User_Role === 'Admin' ? 'bg-indigo-100 text-indigo-700' : ($u->User_Role === 'Guest' ? 'bg-gray-100 text-gray-600' : 'bg-cyan-100 text-cyan-700') }}">
                                        {{ $u->User_Role }}
                                    </span>
                                </td>
                                <td class="py-3 pr-4 hidden sm:table-cell">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $u->Status === 'Active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $u->Status ?? 'Active' }}
                                    </span>
                                </td>
                                <td class="py-3 pr-4 hidden md:table-cell text-gray-500">
                                    {{ $u->Last_Login?->diffForHumans() ?? 'Never' }}
                                </td>
                                <td class="py-3 text-right space-x-2">
                                    <button
                                        type="button"
                                        @click="editingId = {{ $u->User_ID }}"
                                        class="text-indigo-600 hover:underline text-xs font-medium">
                                        Edit
                                    </button>
                                    @if($u->User_ID !== $user->User_ID)
                                        <form method="POST"
                                              action="{{ route('users.destroy', $u->User_ID) }}"
                                              class="inline"
                                              onsubmit="return confirm('Delete {{ $u->User_Name }}? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline text-xs font-medium">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>

                            <!-- Edit row -->
                            <tr x-show="editingId === {{ $u->User_ID }}" x-cloak class="border-b last:border-0 bg-gray-50">
                                <td colspan="6" class="py-4">
                                    <form method="POST" action="{{ route('users.update', $u->User_ID) }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                                        @csrf
                                        @method('PUT')

                                        <div>
                                            <label class="text-xs text-gray-500">Name</label>
                                            <input type="text" name="User_Name" value="{{ $u->User_Name }}"
                                                   class="w-full mt-1 border rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500">
                                        </div>

                                        <div>
                                            <label class="text-xs text-gray-500">Email</label>
                                            <input type="email" name="User_Email" value="{{ $u->User_Email }}"
                                                   class="w-full mt-1 border rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500">
                                        </div>

                                        <div>
                                            <label class="text-xs text-gray-500">Role</label>
                                            <select name="User_Role" class="w-full mt-1 border rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500">
                                                @foreach($roles as $role)
                                                    <option value="{{ $role }}" @selected($u->User_Role === $role)>{{ $role }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label class="text-xs text-gray-500">Status</label>
                                            <select name="Status" class="w-full mt-1 border rounded-lg px-2 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500">
                                                <option value="Active" @selected(($u->Status ?? 'Active') === 'Active')>Active</option>
                                                <option value="Inactive" @selected($u->Status === 'Inactive')>Inactive</option>
                                            </select>
                                        </div>

                                        <div class="md:col-span-4 flex justify-end gap-2 mt-1">
                                            <button type="button" @click="editingId = null"
                                                    class="text-sm text-gray-500 hover:text-gray-700 px-3 py-1.5">
                                                Cancel
                                            </button>
                                            <button type="submit"
                                                    class="bg-indigo-600 text-white text-sm px-4 py-1.5 rounded-lg hover:bg-indigo-700">
                                                Save
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>

        </div>

        <!-- Create user modal -->
        <div
            x-show="showCreate"
            x-cloak
            class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
            @click.self="showCreate = false">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
                <h3 class="text-lg font-semibold mb-4">New User</h3>

                <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="text-sm text-gray-600">Name</label>
                        <input type="text" name="User_Name" value="{{ old('User_Name') }}" required
                               class="w-full mt-1 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Email</label>
                        <input type="email" name="User_Email" value="{{ old('User_Email') }}" required
                               class="w-full mt-1 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div x-data="{ showPassword: false }">
                        <label class="text-sm text-gray-600">Password</label>

                        <div class="relative mt-1">

                            <input
                                :type="showPassword ? 'text' : 'password'"
                                name="User_Password"
                                required
                                minlength="8"
                                class="w-full border rounded-lg px-3 py-2 pr-10 focus:ring-2 focus:ring-indigo-500">

                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                x-data="{ tooltip: false }"
                                @mouseenter="tooltip = true"
                                @mouseleave="tooltip = false"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700">

                                <!-- Eye -->
                                <svg
                                    x-show="!showPassword"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-5 h-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0
                                        3 3 0 016 0zm2.458 0C16.732 15.057
                                        14.157 17 12 17
                                        s-4.732-1.943-5.458-5
                                        C7.268 8.943 9.843 7
                                        12 7s4.732 1.943
                                        5.458 5z"/>
                                </svg>

                                <!-- Eye Off -->
                                <svg
                                    x-show="showPassword"
                                    x-cloak
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-5 h-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19
                                        c-4.478 0-8.268-2.943-9.542-7
                                        a9.956 9.956 0 012.442-3.568
                                        M6.223 6.223A9.953 9.953 0 0112 5
                                        c4.478 0 8.268 2.943 9.542 7
                                        a9.964 9.964 0 01-4.293 5.774
                                        M15 12a3 3 0 00-3-3
                                        m0 0a3 3 0 00-2.12.879
                                        M3 3l18 18"/>
                                </svg>

                                <!-- Tooltip -->
                                <div
                                    x-show="tooltip"
                                    x-transition
                                    x-cloak
                                    class="absolute right-0 top-full mt-2 bg-gray-800 text-white text-xs px-2 py-1 rounded whitespace-nowrap">

                                    <span x-text="showPassword ? 'Hide Password' : 'Show Password'"></span>
                                </div>

                            </button>

                        </div>
                    </div>
                    

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-sm text-gray-600">Role</label>
                            <select name="User_Role" class="w-full mt-1 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                                @foreach($roles as $role)
                                    <option value="{{ $role }}" @selected($role === 'Staff')>{{ $role }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-sm text-gray-600">Status</label>
                            <select name="Status" class="w-full mt-1 border rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                                <option value="Active" selected>Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="showCreate = false"
                                class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2">
                            Cancel
                        </button>
                        <button type="submit"
                                class="bg-indigo-600 text-white text-sm px-5 py-2 rounded-lg hover:bg-indigo-700">
                            Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
    @endif

</div>

@endsection