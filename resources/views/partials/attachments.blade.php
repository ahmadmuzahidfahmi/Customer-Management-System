<div class="bg-white rounded-lg shadow p-4 mt-6" x-data="{
    addingFile: false,
    selected: [],
    allIds: {{ Js::from($attachments->pluck('Attachment_ID')) }},
    toggleAll() {
        this.selected = this.selected.length === this.allIds.length ? [] : [...this.allIds];
    }
}">

    <!-- Hidden form the checkboxes submit into -->
    <form id="bulk-attachment-form" method="POST" action="{{ route('attachments.bulkDestroy') }}">
        @csrf
        @method('DELETE')
        <input type="hidden" name="delete_backup" value="0" x-ref="bulkDeleteBackupInput">
    </form>

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Attachments</h2>

        @unless(auth()->user()?->isGuest())
        <button
            @click="addingFile = !addingFile"
            type="button"
            class="w-8 h-8 flex items-center justify-center rounded-full bg-cyan-600 text-white hover:bg-cyan-700 text-lg leading-none">
            <span x-text="addingFile ? '✕' : '+'"></span>
        </button>
        @endunless
    </div>

    <!-- Upload Form -->
    <div x-show="addingFile" x-cloak class="space-y-2 mb-6 border rounded-lg p-3 bg-gray-50">
        <form method="POST" action="{{ route('attachments.store') }}" enctype="multipart/form-data" class="space-y-2">
            @csrf
            <input type="hidden" name="Entity_Type" value="{{ $entityType }}">
            <input type="hidden" name="Entity_ID" value="{{ $entityId }}">

            <input
                type="file"
                name="file[]"
                multiple
                required
                class="w-full border rounded-lg px-3 py-2 text-sm bg-white">

            <p class="text-xs text-gray-500">Max 10MB each. Images, PDF, Word, Excel, or text files. You can select multiple files.</p>

            <button
                type="submit"
                class="bg-cyan-600 text-white px-4 py-2 rounded-lg hover:bg-cyan-700 text-sm">
                Upload
            </button>
        </form>
    </div>

    @if($attachments->count() > 0)
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
                    if (!confirm(`Delete ${selected.length} selected file(s)?`)) return;
                    const deleteBackup = confirm('Also delete the backup copies in Google Drive?');
                    $refs.bulkDeleteBackupInput.value = deleteBackup ? '1' : '0';
                    document.getElementById('bulk-attachment-form').submit();
                "
                class="bg-red-600 text-white px-3 py-1.5 rounded-lg hover:bg-red-700 text-sm">
                Delete Selected (<span x-text="selected.length"></span>)
            </button>
        </div>
        @endunless
    @endif

    <!-- File List -->
    <div class="space-y-2">
        @forelse($attachments as $attachment)
            <div class="flex items-center justify-between border rounded-lg p-3">

                <div class="flex items-center gap-3 min-w-0">
                    @unless(auth()->user()?->isGuest())
                    <input
                        type="checkbox"
                        form="bulk-attachment-form"
                        name="ids[]"
                        value="{{ $attachment->Attachment_ID }}"
                        x-model="selected"
                        class="shrink-0">
                    @endunless

                    <a href="{{ route('attachments.show', $attachment->Attachment_ID) }}"
                       target="_blank"
                       class="flex items-center gap-3 min-w-0 hover:opacity-80">

                        @if($attachment->isImage())
                            <img src="{{ route('attachments.show', $attachment->Attachment_ID) }}"
                                 alt="{{ $attachment->Original_Name }}"
                                 class="w-12 h-12 object-cover rounded-lg border shrink-0">
                        @else
                            <div class="w-12 h-12 flex items-center justify-center rounded-lg bg-cyan-100 text-cyan-700 text-xs font-semibold shrink-0">
                                {{ strtoupper(pathinfo($attachment->Original_Name, PATHINFO_EXTENSION)) }}
                            </div>
                        @endif

                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $attachment->Original_Name }}</p>
                            @if($attachment->Entity_Type !== $entityType)
                            <p class="text-xs text-cyan-600 mt-0.5">
                                via {{ $attachment->Entity_Type }}: {{ $attachment->entityLabel() }}
                            </p>
                            @endif
                            <p class="text-xs text-gray-500">
                                {{ $attachment->humanSize() }} ·
                                {{ $attachment->uploader->User_Name ?? 'Unknown' }} ·
                                {{ $attachment->Created_At->diffForHumans() }}
                            </p>
                        </div>
                    </a>
                </div>

                @unless(auth()->user()?->isGuest())
                <form
                    method="POST"
                    action="{{ route('attachments.destroy', $attachment->Attachment_ID) }}"
                    x-data
                    @submit.prevent="
                        if (!confirm('Delete this file?')) return;
                        const deleteBackup = confirm('Also delete the backup copy in Google Drive?');
                        $refs.deleteBackupInput.value = deleteBackup ? '1' : '0';
                        $el.submit();
                    "
                    class="shrink-0">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="delete_backup" value="0" x-ref="deleteBackupInput">
                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                </form>
                @endunless

            </div>
        @empty
            <p class="text-sm text-gray-500">No attachments yet.</p>
        @endforelse
    </div>

</div>