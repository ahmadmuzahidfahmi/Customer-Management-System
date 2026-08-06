<div class="bg-white rounded-lg shadow p-4 mt-6" x-data="{
    addingNote: false,
    selected: [],
    allIds: {{ Js::from($notes->pluck('Note_ID')) }},
    toggleAll() {
        this.selected = this.selected.length === this.allIds.length ? [] : [...this.allIds];
    }
}">

    <!-- Hidden form the checkboxes submit into -->
    <form id="bulk-note-form" method="POST" action="{{ route('notes.bulkDestroy') }}">
        @csrf
        @method('DELETE')
    </form>

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Notes</h2>

        @unless(auth()->user()?->isGuest())
        <button
            @click="addingNote = !addingNote"
            type="button"
            class="w-8 h-8 flex items-center justify-center rounded-full bg-cyan-600 text-white hover:bg-cyan-700 text-lg leading-none">
            <span x-text="addingNote ? '✕' : '+'"></span>
        </button>
        @endunless

    </div>

    <!-- Add Note Form (collapsed by default) -->
    <div x-show="addingNote" x-cloak class="space-y-2 mb-6 border rounded-lg p-3 bg-gray-50">
        <form method="POST" action="{{ route('notes.store') }}" class="space-y-2">
            @csrf
            <input type="hidden" name="{{ $ownerField }}" value="{{ $ownerId }}">

            <input
                type="text"
                name="Subject"
                placeholder="Subject (optional)"
                class="w-full border rounded-lg px-3 py-2 text-sm">

            <textarea
                name="Content"
                rows="3"
                placeholder="Write a note..."
                required
                class="w-full border rounded-lg px-3 py-2 text-sm"></textarea>

            <button
                type="submit"
                class="bg-cyan-600 text-white px-4 py-2 rounded-lg hover:bg-cyan-700 text-sm">
                Add Note
            </button>
        </form>
    </div>

    @if($notes->count() > 0)
        @unless(auth()->user()?->isGuest())
        <!-- Select-all + bulk action bar -->
        <div class="flex items-center justify-between mb-3 pb-3 border-b">
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input
                    type="checkbox"
                    :checked="selected.length === allIds.length"
                    @change="toggleAll()">
                Select all
            </label>

            <button
                type="button"
                x-show="selected.length > 0"
                x-cloak
                @click="
                    if (!confirm(`Delete ${selected.length} selected note(s)?`)) return;
                    document.getElementById('bulk-note-form').submit();
                "
                class="bg-red-600 text-white px-3 py-1.5 rounded-lg hover:bg-red-700 text-sm">
                Delete Selected (<span x-text="selected.length"></span>)
            </button>
        </div>
        @endunless
    @endif

    <!-- Notes List -->
<div class="space-y-3">
    @forelse($notes as $note)
        <div class="border rounded-lg p-3" x-data="{ editing: false }">

            <!-- View mode -->
            <div x-show="!editing">
                <div class="flex justify-between items-start">
                    <div class="flex items-start gap-3 min-w-0">
                        @unless(auth()->user()?->isGuest())
                        <input
                            type="checkbox"
                            form="bulk-note-form"
                            name="ids[]"
                            value="{{ $note->Note_ID }}"
                            x-model="selected"
                            class="mt-1 shrink-0">
                        @endunless

                        <div>
                            @if($note->Subject)
                                <p class="font-semibold text-gray-800">{{ $note->Subject }}</p>
                            @endif
                            <p class="text-gray-700 text-sm mt-1">{{ $note->Content }}</p>
                        </div>
                    </div>

                <div class="flex items-center gap-2">
                @unless(auth()->user()?->isGuest())
                    <button
                        @click="editing = true"
                        type="button"
                        class="text-cyan-600 hover:text-cyan-800 text-sm">
                        Edit
                    </button>

                    <form
                        method="POST"
                        action="{{ route('notes.destroy', $note->Note_ID) }}"
                        class="flex items-center m-0">
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="text-red-600 hover:text-red-800 text-sm">
                            Delete
                        </button>
                @endunless
                    </form>

                </div>

                </div>
                <p class="text-xs text-gray-400 mt-2">
                    {{ $note->Created_At->diffForHumans() }}
                </p>
            </div>

            <!-- Edit mode -->
            <div x-show="editing" x-cloak>
                <form method="POST" action="{{ route('notes.update', $note->Note_ID) }}" class="space-y-2">
                    @csrf
                    @method('PUT')

                    <input
                        type="text"
                        name="Subject"
                        value="{{ $note->Subject }}"
                        placeholder="Subject (optional)"
                        class="w-full border rounded-lg px-3 py-2 text-sm font-semibold">

                    <textarea
                        name="Content"
                        rows="3"
                        required
                        class="w-full border rounded-lg px-3 py-2 text-sm">{{ $note->Content }}</textarea>

                    <div class="flex gap-2">
                        <button
                            type="submit"
                            class="bg-cyan-600 text-white px-3 py-1.5 rounded-lg hover:bg-cyan-700 text-sm">
                            Save
                        </button>

                        <button
                            @click="editing = false"
                            type="button"
                            class="bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg hover:bg-gray-300 text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

        </div>
    @empty
        <p class="text-sm text-gray-500">No notes yet.</p>
    @endforelse
</div>

</div>