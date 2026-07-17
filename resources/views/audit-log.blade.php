@extends('layouts.app')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Audit Log</h1>
</div>

<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form method="GET" action="{{ route('audit-log') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">

        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search description..."
            class="border rounded-lg px-3 py-2">

        <select name="action" class="border rounded-lg px-3 py-2">
            <option value="">All Actions</option>
            @foreach(['created','updated','deleted','restored','force_deleted','viewed','login','logout'] as $action)
                <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_',' ',$action)) }}
                </option>
            @endforeach
        </select>

        <select name="type" class="border rounded-lg px-3 py-2">
            <option value="">All Types</option>
            @foreach(['Customer','Contact','Lead','Note','Auth','Page'] as $type)
                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
            @endforeach
        </select>

        <div class="flex gap-2">
            <button type="submit" class="bg-cyan-600 text-white px-4 py-2 rounded-lg hover:bg-cyan-700">Filter</button>
            <a href="{{ route('audit-log') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">Reset</a>
        </div>

    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">Time</th>
                    <th class="px-6 py-3 text-left">User</th>
                    <th class="px-6 py-3 text-left">Action</th>
                    <th class="px-6 py-3 text-left">Type</th>
                    <th class="px-6 py-3 text-left">Description</th>
                    <th class="px-6 py-3 text-left">IP Address</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($logs as $log)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">{{ $log->Created_At?->diffForHumans() }}</td>
                    <td class="px-6 py-4">{{ $log->user->User_Name ?? 'System' }}</td>
                    <td class="px-6 py-4">
                        @php
                        $actionColors = [
                            'created' => 'bg-green-100 text-green-700',
                            'updated' => 'bg-blue-100 text-blue-700',
                            'deleted' => 'bg-red-100 text-red-700',
                            'restored' => 'bg-cyan-100 text-cyan-700',
                            'force_deleted' => 'bg-red-200 text-red-800',
                            'viewed' => 'bg-gray-100 text-gray-600',
                            'login' => 'bg-purple-100 text-purple-700',
                            'logout' => 'bg-gray-100 text-gray-500',
                        ];
                        @endphp
                        <span class="px-2 py-1 text-xs rounded-full {{ $actionColors[$log->Action] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ ucfirst(str_replace('_',' ',$log->Action)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">{{ $log->Auditable_Type }}</td>
                    <td class="px-6 py-4">{{ $log->Description }}</td>
                    <td class="px-6 py-4 text-gray-400">{{ $log->IP_Address }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="px-6 py-4">
    {{ $logs->links() }}
</div>

@endsection