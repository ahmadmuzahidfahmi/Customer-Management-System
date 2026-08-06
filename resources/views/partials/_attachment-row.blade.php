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