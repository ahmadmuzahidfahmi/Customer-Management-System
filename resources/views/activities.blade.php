@php
    $activityTypes = ['Call', 'Email', 'Meeting', 'Follow-Up', 'Other'];
    $users = \App\Models\User::orderBy('User_Name')->get();
@endphp

<div class="bg-white rounded-lg shadow p-4 mt-6" x-data="{ addingActivity: false }">

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Activities</h2>

        <button
            @click="addingActivity = !addingActivity"
            type="button"
            class="w-8 h-8 flex items-center justify-center rounded-full bg-cyan-600 text-white hover:bg-cyan-700 text-lg leading-none">
            <span x-text="addingActivity ? '✕' : '+'"></span>
        </button>
    </div>

    <!-- Log Activity Form (collapsed by default) -->
    <div x-show="addingActivity" x-cloak class="space-y-2 mb-6 border rounded-lg p-3 bg-gray-50">
        <form method="POST" action="{{ route('activities.store') }}" class="space-y-2">
            @csrf
            <input type="hidden" name="{{ $ownerField }}" value="{{ $ownerId }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <select name="Activity_Type" required class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="">Type...</option>
                    @foreach($activityTypes as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>

                <input
                    type="date"
                    name="Dead_Line"
                    class="w-full border rounded-lg px-3 py-2 text-sm"
                    placeholder="Deadline (optional)">
            </div>

            <input
                type="text"
                name="Subject"
                placeholder="Subject"
                required
                class="w-full border rounded-lg px-3 py-2 text-sm">

            <textarea
                name="Activity_Detail"
                rows="2"
                placeholder="Details (optional)"
                class="w-full border rounded-lg px-3 py-2 text-sm"></textarea>

            <select name="Assigned_To" class="w-full border rounded-lg px-3 py-2 text-sm">
                <option value="">Assign to me</option>
                @foreach($users as $u)
                    <option value="{{ $u->User_ID }}">{{ $u->User_Name }}</option>
                @endforeach
            </select>

            <button
                type="submit"
                class="bg-cyan-600 text-white px-4 py-2 rounded-lg hover:bg-cyan-700 text-sm">
                Log Activity
            </button>
        </form>
    </div>

    <!-- Activity List -->
    <div class="space-y-3">
        @forelse($activities as $activity)
            <div class="border rounded-lg p-3 {{ $activity->isOverdue() ? 'border-red-300 bg-red-50' : '' }}" x-data="{ editing: false }">

                <!-- View mode -->
                <div x-show="!editing">
                    <div class="flex justify-between items-start gap-2">
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-cyan-100 text-cyan-700">
                                    {{ $activity->Activity_Type }}
                                </span>
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full
                                    {{ $activity->Status === 'Completed' ? 'bg-green-100 text-green-700' :
                                       ($activity->Status === 'Cancelled' ? 'bg-gray-200 text-gray-600' : 'bg-yellow-100 text-yellow-700') }}">
                                    {{ $activity->Status }}
                                </span>
                                @if($activity->isOverdue())
                                    <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-red-100 text-red-700">
                                        Overdue
                                    </span>
                                @endif
                            </div>
                            <p class="font-semibold text-gray-800 mt-1">{{ $activity->Subject }}</p>
                            @if($activity->Activity_Detail)
                                <p class="text-gray-700 text-sm mt-1">{{ $activity->Activity_Detail }}</p>
                            @endif
                            <p class="text-xs text-gray-500 mt-2">
                                @if($activity->Dead_Line)
                                    Due {{ $activity->Dead_Line->format('d M Y') }} ·
                                @endif
                                Assigned to {{ $activity->assignee->User_Name ?? 'Unassigned' }}
                                · {{ $activity->Created_At->diffForHumans() }}
                            </p>
                        </div>

                        <div class="flex flex-col items-end gap-1 shrink-0">
                            @if($activity->Status === 'Pending')
                                <form method="POST" action="{{ route('activities.complete', $activity->Activity_ID) }}">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800 text-sm">Complete</button>
                                </form>
                                <form method="POST" action="{{ route('activities.cancel', $activity->Activity_ID) }}">
                                    @csrf
                                    <button type="submit" class="text-gray-500 hover:text-gray-700 text-sm">Cancel</button>
                                </form>
                            @endif
                            <button @click="editing = true" type="button" class="text-cyan-600 hover:text-cyan-800 text-sm">Edit</button>
                            <form method="POST" action="{{ route('activities.destroy', $activity->Activity_ID) }}"
                                  onsubmit="return confirm('Delete this activity?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Edit mode -->
                <div x-show="editing" x-cloak>
                    <form method="POST" action="{{ route('activities.update', $activity->Activity_ID) }}" class="space-y-2">
                        @csrf
                        @method('PUT')

                        <select name="Activity_Type" required class="w-full border rounded-lg px-3 py-2 text-sm">
                            @foreach($activityTypes as $type)
                                <option value="{{ $type }}" @selected($activity->Activity_Type === $type)>{{ $type }}</option>
                            @endforeach
                        </select>

                        <input type="text" name="Subject" value="{{ $activity->Subject }}" required
                               class="w-full border rounded-lg px-3 py-2 text-sm font-semibold">

                        <textarea name="Activity_Detail" rows="2"
                                  class="w-full border rounded-lg px-3 py-2 text-sm">{{ $activity->Activity_Detail }}</textarea>

                        <input type="date" name="Dead_Line"
                               value="{{ $activity->Dead_Line?->format('Y-m-d') }}"
                               class="w-full border rounded-lg px-3 py-2 text-sm">

                        <div class="flex gap-2">
                            <button type="submit" class="bg-cyan-600 text-white px-3 py-1.5 rounded-lg hover:bg-cyan-700 text-sm">Save</button>
                            <button @click="editing = false" type="button" class="bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg hover:bg-gray-300 text-sm">Cancel</button>
                        </div>
                    </form>
                </div>

            </div>
        @empty
            <p class="text-sm text-gray-500">No activities logged yet.</p>
        @endforelse
    </div>

</div>