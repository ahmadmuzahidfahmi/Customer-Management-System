@extends('layouts.app')

@section('content')

<div class="space-y-6">

    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Activities</h1>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('activities.index') }}" class="flex flex-wrap gap-3 items-end">

            <div>
                <label class="text-xs text-gray-500 block mb-1">Status</label>
                <select name="status" class="border rounded-lg px-3 py-2 text-sm">
                    <option value="">All</option>
                    @foreach(['Pending', 'Completed', 'Cancelled'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs text-gray-500 block mb-1">Type</label>
                <select name="type" class="border rounded-lg px-3 py-2 text-sm">
                    <option value="">All</option>
                    @foreach(['Call', 'Email', 'Meeting', 'Follow-Up', 'Other'] as $type)
                        <option value="{{ $type }}" @selected(request('type') === $type)>{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <label class="flex items-center gap-2 text-sm text-gray-700 pb-2">
                <input type="checkbox" name="mine" value="1" @checked(request('mine'))>
                My activities only
            </label>

            <button type="submit" class="bg-cyan-600 text-white px-4 py-2 rounded-lg hover:bg-cyan-700 text-sm">
                Filter
            </button>

            @if(request()->anyFilled(['status', 'type', 'mine']))
                <a href="{{ route('activities.index') }}" class="text-sm text-gray-500 hover:text-gray-700 pb-2">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- List -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="px-4 py-3">Type</th>
                    <th class="px-4 py-3">Subject</th>
                    <th class="px-4 py-3">Linked To</th>
                    <th class="px-4 py-3">Assigned</th>
                    <th class="px-4 py-3">Deadline</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($activities as $activity)
                    <tr class="border-b {{ $activity->isOverdue() ? 'bg-red-50' : '' }}">
                        <td class="px-4 py-3">{{ $activity->Activity_Type }}</td>
                        <td class="px-4 py-3 font-medium">{{ $activity->Subject }}</td>
                        <td class="px-4 py-3">
                            @if($activity->lead)
                                <a href="{{ route('leads.show', $activity->lead->Lead_ID) }}" class="text-cyan-600 hover:text-cyan-800">
                                    {{ $activity->lead->Lead_Name }}
                                </a>
                            @elseif($activity->contact)
                                <a href="{{ route('contacts.show', $activity->contact->Contact_ID) }}" class="text-cyan-600 hover:text-cyan-800">
                                    {{ $activity->contact->Contact_Name }}
                                </a>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $activity->assignee->User_Name ?? 'Unassigned' }}</td>
                        <td class="px-4 py-3">
                            {{ $activity->Dead_Line?->format('d M Y') ?? '—' }}
                            @if($activity->isOverdue())
                                <span class="text-xs text-red-600 font-medium ml-1">Overdue</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full
                                {{ $activity->Status === 'Completed' ? 'bg-green-100 text-green-700' :
                                   ($activity->Status === 'Cancelled' ? 'bg-gray-200 text-gray-600' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ $activity->Status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($activity->Status === 'Pending')
                                <form method="POST" action="{{ route('activities.complete', $activity->Activity_ID) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800">Complete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            No activities found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $activities->links() }}

</div>

@endsection