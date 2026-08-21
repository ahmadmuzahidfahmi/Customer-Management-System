@extends('layouts.app')

@section('content')

<div class="space-y-6" x-data="{ editing: false }">

    <!-- Header -->
    <div class="flex justify-between items-center">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                {{ $activity->Subject }}
            </h1>

            <p class="text-gray-500">
                {{ $activity->Activity_Type }}
            </p>
        </div>

        @unless(auth()->user()?->isGuest())
        <div class="flex gap-2">

            @if($activity->Status === 'Pending')
                <form method="POST" action="{{ route('activities.complete', $activity->Activity_ID) }}">
                    @csrf
                    <button
                        type="submit"
                        class="flex items-center justify-center px-4 h-12 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Mark Complete
                    </button>
                </form>

                <form method="POST" action="{{ route('activities.cancel', $activity->Activity_ID) }}">
                    @csrf
                    <button
                        type="submit"
                        class="flex items-center justify-center px-4 h-12 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Cancel Activity
                    </button>
                </form>
            @endif

            <button
                type="button"
                @click="editing = !editing"
                class="flex items-center justify-center w-28 h-12 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700">
                <span x-text="editing ? 'Close' : 'Edit'"></span>
            </button>

            <form method="POST"
                  action="{{ route('activities.destroy', $activity->Activity_ID) }}"
                  onsubmit="return confirm('Permanently delete this activity? This cannot be undone.');">
                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="flex items-center justify-center w-28 h-12 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Delete
                </button>
            </form>

        </div>
        @endunless

    </div>

    <!-- Activity Information -->
    <div class="bg-white rounded-lg shadow p-6">

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">
                Activity Information
            </h2>

            @unless(auth()->user()?->isGuest())
            <span x-show="!editing" class="text-xs text-gray-400">Click Edit to make changes</span>
            @endunless
        </div>

        <!-- View mode -->
        <div x-show="!editing">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <p class="text-sm text-gray-500">Type</p>
                    <p class="font-medium">{{ $activity->Activity_Type }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500 mb-1">Status</p>
                    <span class="px-2 py-1 text-xs rounded-full
                        {{ $activity->Status === 'Completed' ? 'bg-green-100 text-green-700' :
                           ($activity->Status === 'Cancelled' ? 'bg-gray-200 text-gray-600' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ $activity->Status }}
                    </span>
                    @if($activity->isOverdue())
                        <span class="ml-1 px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Overdue</span>
                    @endif
                </div>

                <div>
                    <p class="text-sm text-gray-500">Deadline</p>
                    <p class="font-medium">
                        {{ $activity->Dead_Line?->format('d M Y, h:i A') ?? 'No deadline set' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Assigned To</p>
                    <p class="font-medium">{{ $activity->assignee->User_Name ?? 'Unassigned' }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Logged By</p>
                    <p class="font-medium">{{ $activity->creator->User_Name ?? 'Unknown' }}</p>
                </div>

                @if($activity->Status === 'Completed')
                <div>
                    <p class="text-sm text-gray-500">Completed At</p>
                    <p class="font-medium">{{ $activity->Completed_At?->format('d M Y, h:i A') ?? 'N/A' }}</p>
                </div>
                @endif

                <div class="md:col-span-2">
                    <p class="text-sm text-gray-500">Details</p>
                    <p class="font-medium whitespace-pre-line">{{ $activity->Activity_Detail ?: 'No details added.' }}</p>
                </div>

            </div>

        </div>

        <!-- Edit mode -->
        @unless(auth()->user()?->isGuest())
        <div x-show="editing" x-cloak>
            <form method="POST" action="{{ route('activities.update', $activity->Activity_ID) }}" class="space-y-3">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                    <div>
                        <label class="block text-sm font-medium mb-1">Type</label>
                        <select name="Activity_Type" required class="w-full border rounded-lg px-3 py-2 text-sm">
                            @foreach(['Call','Email','Meeting','Follow-Up','Other'] as $type)
                                <option value="{{ $type }}" @selected($activity->Activity_Type === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Deadline</label>
                        <input
                            type="datetime-local"
                            name="Dead_Line"
                            value="{{ $activity->Dead_Line?->format('Y-m-d\TH:i') }}"
                            class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>

                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Subject</label>
                    <input
                        type="text"
                        name="Subject"
                        value="{{ $activity->Subject }}"
                        required
                        class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Details</label>
                    <textarea
                        name="Activity_Detail"
                        rows="4"
                        class="w-full border rounded-lg px-3 py-2 text-sm">{{ $activity->Activity_Detail }}</textarea>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="editing = false" class="px-4 py-2 rounded-lg bg-gray-200 text-sm">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-cyan-600 text-white text-sm hover:bg-cyan-700">
                        Save Changes
                    </button>
                </div>

            </form>
        </div>
        @endunless

    </div>

    <!-- Notes -->
    @include('partials.notes', [
        'notes' => $activity->notes,
        'ownerField' => 'Activity_ID',
        'ownerId' => $activity->Activity_ID,
    ])

    <!-- Linked To -->
    <div class="bg-white rounded-lg shadow p-6">

        <h2 class="text-lg font-semibold mb-4">
            Linked To
        </h2>

        @if($activity->lead)
            <div
                onclick="window.location='{{ route('leads.show', $activity->lead->Lead_ID) }}'"
                class="flex justify-between items-center border-b py-4 cursor-pointer hover:bg-cyan-50 transition rounded-lg px-3">

                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Lead</p>
                    <p class="font-medium">{{ $activity->lead->Lead_Name }}</p>
                </div>

                <span class="text-cyan-600">View Lead →</span>
            </div>
        @endif

        @if($activity->contact)
            <div
                onclick="window.location='{{ route('contacts.show', $activity->contact->Contact_ID) }}'"
                class="flex justify-between items-center py-4 cursor-pointer hover:bg-cyan-50 transition rounded-lg px-3">

                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Contact</p>
                    <p class="font-medium">{{ $activity->contact->Contact_Name }}</p>
                </div>

                <span class="text-cyan-600">View Contact →</span>
            </div>
        @endif

        @if(!$activity->lead && !$activity->contact)
            <p class="text-sm text-gray-400">Not linked to a lead or contact.</p>
        @endif

    </div>

    <!-- Attachments -->
    @include('partials.attachments', [
        'attachments' => $activity->attachments,
        'entityType' => 'Activity',
        'entityId' => $activity->Activity_ID,
    ])

</div>

@endsection
