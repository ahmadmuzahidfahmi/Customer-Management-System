<div class="bg-white rounded-lg shadow p-4 mt-6" x-data="{ addingFile: false }">

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Attachments</h2>

        <button
            @click="addingFile = !addingFile"
            type="button"
            class="w-8 h-8 flex items-center justify-center rounded-full bg-cyan-600 text-white hover:bg-cyan-700 text-lg leading-none">
            <span x-text="addingFile ? '✕' : '+'"></span>
        </button>
    </div>

    <!-- Upload Form -->
    <div x-show="addingFile" x-cloak class="space-y-2 mb-6 border rounded-lg p-3 bg-gray-50">
        <form method="POST" action="{{ route('attachments.store') }}" enctype="multipart/form-data" class="space-y-2">
            @csrf
            <input type="hidden" name="Entity_Type" value="{{ $entityType }}">
            <input type="hidden" name="Entity_ID" value="{{ $entityId }}">

            <input
                type="file"
                name="file"
                required
                class="w-full border rounded-lg px-3 py-2 text-sm bg-white">

            <p class="text-xs text-gray-500">Max 10MB. Images, PDF, Word, Excel, or text files.</p>

            <button
                type="submit"
                class="bg-cyan-600 text-white px-4 py-2 rounded-lg hover:bg-cyan-700 text-sm">
                Upload
            </button>
        </form>
    </div>

    <!-- File List -->
    <div class="space-y-2">
        @forelse($attachments as $attachment)
            <div class="flex items-center justify-between border rounded-lg p-3">

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

                <form method="POST" action="{{ route('attachments.destroy', $attachment->Attachment_ID) }}"
                      onsubmit="return confirm('Delete this file?');" class="shrink-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                </form>

            </div>
        @empty
            <p class="text-sm text-gray-500">No attachments yet.</p>
        @endforelse
    </div>

</div>