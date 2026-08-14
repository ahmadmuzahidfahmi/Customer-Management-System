@extends('layouts.app')

@section('content')

<div class="space-y-6">

    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Activities</h1>
    </div>

    <!-- Due Today -->
@if($dueToday->count())
<div class="bg-red-50 border border-yellow-200 rounded-lg shadow p-4">
    
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-lg font-semibold text-yellow-800">
            Due Today ({{ $dueToday->count() }})
        </h2>

        <span class="text-sm text-yellow-700">
            {{ now()->format('d M Y') }}
        </span>
    </div>

    <div class="space-y-2">

        @foreach($dueToday as $activity)

            <a href="{{ route('activities.show', $activity->Activity_ID) }}"
               class="block bg-white rounded-lg border p-3 hover:bg-red-100 transition">

                <div class="flex justify-between items-center">

                    <div>
                        <p class="font-medium text-gray-800">
                            {{ $activity->Subject }}
                        </p>

                        <p class="text-sm text-gray-500">
                            {{ $activity->lead->Lead_Name
                                ?? $activity->contact->Contact_Name
                                ?? 'Unlinked' }}
                        </p>
                    </div>

                    <div class="text-right">

                        <p class="text-sm font-medium text-yellow-700">
                            {{ $activity->Dead_Line?->format('h:i A') }}
                        </p>

                        <p class="text-xs text-gray-500">
                            {{ $activity->Activity_Type }}
                        </p>

                    </div>

                </div>

            </a>

        @endforeach

    </div>

</div>
@endif

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
                    <th class="px-4 py-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activities as $activity)
                         <tr
                        onclick="window.location='{{ route('activities.show', $activity->Activity_ID) }}'"
                        class="border-b cursor-pointer hover:bg-gray-50 transition
                            {{ $activity->isOverdue() ? 'bg-red-50 hover:bg-red-100' : '' }}">                        <td class="px-4 py-3">{{ $activity->Activity_Type }}</td>
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
                                @unless(auth()->user()?->isGuest())
                                <form method="POST" action="{{ route('activities.complete', $activity->Activity_ID) }}" class="inline ">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800">Complete</button>
                                </form>
                                @endunless
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

<!-- Pagination Controls -->
<div class="px-6 py-4 border-t">
    <div class="flex items-center justify-center gap-2 mt-6">

        @if ($activities->onFirstPage())
            <span class="px-4 py-2 bg-gray-100 text-gray-400 rounded-lg">
                Previous
            </span>
        @else
            <a href="{{ $activities->previousPageUrl() }}"
               class="px-4 py-2 bg-white border rounded-lg hover:bg-gray-50">
                Previous
            </a>
        @endif

        @for ($i = 1; $i <= $activities->lastPage(); $i++)
            <a href="{{ $activities->url($i) }}"
               class="px-4 py-2 rounded-lg
               {{ $activities->currentPage() == $i
                   ? 'bg-cyan-600 text-white'
                   : 'bg-white border hover:bg-gray-50' }}">
                {{ $i }}
            </a>
        @endfor

        @if ($activities->hasMorePages())
            <a href="{{ $activities->nextPageUrl() }}"
               class="px-4 py-2 bg-white border rounded-lg hover:bg-gray-50">
                Next
            </a>
        @else
            <span class="px-4 py-2 bg-gray-100 text-gray-400 rounded-lg">
                Next
            </span>
        @endif

    </div>
</div>

</div>

@endsection