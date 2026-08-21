@extends('layouts.app')

@section('content')

@php
    $folderLabels = [
        'Contacts' => 'Contacts',
        'Company' => 'Customers',
        'Leads' => 'Leads',
        'Activity' => 'Activities',
        'Notes' => 'Notes',
    ];
@endphp

<div class="space-y-6" x-data="{ tab: '{{ request('tab', array_key_first($sections)) }}' }">

    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Attachment Backups</h1>
            <p class="text-gray-500 text-sm">Live contents of the Google Drive backup, per folder, cross-checked against local storage.</p>
        </div>

        <form method="POST" action="{{ route('attachments.verifyAll') }}">
            @csrf
            <button
                type="submit"
                class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium">
                Verify All
            </button>
        </form>
    </div>

    <!-- KPI Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <div class="bg-white rounded-xl shadow p-5 border-l-4 border-cyan-500">
            <p class="text-sm text-gray-500">Files on Drive</p>
            <p class="text-3xl font-bold text-cyan-600">{{ $totalDriveFiles }}</p>
        </div>

        <div class="bg-white rounded-xl shadow p-5 border-l-4 border-green-500">
            <p class="text-sm text-gray-500">Tracked in System</p>
            <p class="text-3xl font-bold text-green-600">{{ $totalTracked }}</p>
        </div>

        <div class="bg-white rounded-xl shadow p-5 border-l-4 border-amber-500">
            <p class="text-sm text-gray-500">Untracked on Drive</p>
            <p class="text-3xl font-bold text-amber-600">{{ $totalUntracked }}</p>
        </div>

        <div class="bg-white rounded-xl shadow p-5 border-l-4 border-red-500">
            <p class="text-sm text-gray-500">Missing Local Copy</p>
            <p class="text-3xl font-bold text-red-600">{{ $localMissingCount }}</p>
        </div>

    </div>

    <!-- Folder Tabs -->
    <div class="flex gap-2 border-b overflow-x-auto">
        @foreach($sections as $entityType => $section)
            <button
                @click="tab = '{{ $entityType }}'"
                :class="tab === '{{ $entityType }}' ? 'border-cyan-600 text-cyan-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                class="px-4 py-2 border-b-2 font-medium text-sm whitespace-nowrap">
                {{ $folderLabels[$entityType] ?? $entityType }}
                ({{ $section['tracked']->count() + $section['untracked']->count() }})
            </button>
        @endforeach
    </div>

    @foreach($sections as $entityType => $section)
        <div x-show="tab === '{{ $entityType }}'" x-cloak class="space-y-6">

            <!-- Tracked files -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-3 border-b bg-gray-50">
                    <h2 class="font-semibold text-gray-700 text-sm">Tracked in System ({{ $section['tracked']->count() }})</h2>
                </div>

                <div class="divide-y">
                    @forelse($section['tracked'] as $attachment)
                        <div class="flex items-center justify-between px-6 py-4 gap-4">

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
                                    <p class="text-xs mt-0.5">
                                        @if($attachment->entityUrl())
                                            <span class="text-cyan-600">{{ $attachment->entityLabel() }}</span>
                                        @else
                                            <span class="text-gray-400">{{ $attachment->entityLabel() }}</span>
                                        @endif
                                    </p>
                                </div>
                            </a>

                            <div class="flex items-center gap-4 shrink-0">

                                <div class="flex flex-col items-end gap-1">
                                    <span class="text-xs font-medium px-2 py-0.5 rounded-full
                                        {{ $attachment->isFullySynced() ? 'bg-green-100 text-green-700' :
                                           (! $attachment->Is_On_Local && ! $attachment->Is_On_Drive ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                                        {{ $attachment->syncStatusLabel() }}
                                    </span>
                                    <span class="text-xs text-gray-400">
                                        Local: {{ $attachment->Is_On_Local ? '✓' : '✕' }} ·
                                        Drive: {{ $attachment->Is_On_Drive ? '✓' : '✕' }}
                                    </span>
                                </div>

                                <div class="flex flex-col items-end gap-1">
                                    @if(! $attachment->isFullySynced())
                                        <form method="POST" action="{{ route('attachments.resync', $attachment->Attachment_ID) }}">
                                            @csrf
                                            <button type="submit" class="text-cyan-600 hover:text-cyan-800 text-sm font-medium">
                                                Restore
                                            </button>
                                        </form>
                                    @endif

                                    <form
                                        method="POST"
                                        action="{{ route('attachments.destroy', $attachment->Attachment_ID) }}"
                                        x-data
                                        @submit.prevent="
                                            if (!confirm('Delete this file?')) return;
                                            const deleteBackup = confirm('Also delete the backup copy in Google Drive?');
                                            $refs.deleteBackupInput.value = deleteBackup ? '1' : '0';
                                            $el.submit();
                                        ">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="delete_backup" value="0" x-ref="deleteBackupInput">
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            Delete
                                        </button>
                                    </form>
                                </div>

                            </div>

                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-gray-500 text-sm">
                            No tracked files in this folder.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Untracked Drive-only files -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-3 border-b bg-amber-50">
                    <h2 class="font-semibold text-amber-800 text-sm">Untracked on Drive ({{ $section['untracked']->count() }})</h2>
                    <p class="text-xs text-amber-700 mt-0.5">Files sitting in this Drive folder with no matching record in the system — usually left behind after a "delete but keep backup" action.</p>
                </div>

                <div class="divide-y">
                    @forelse($section['untracked'] as $file)
                        <div class="flex items-center justify-between px-6 py-4 gap-4">

                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $file['name'] }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $file['size'] !== null ? number_format($file['size'] / 1024, 1) . ' KB' : 'Unknown size' }}
                                    @if($file['modified'])
                                        · modified {{ \Illuminate\Support\Carbon::createFromTimestamp($file['modified'])->diffForHumans() }}
                                    @endif
                                </p>
                            </div>

                            <div class="flex items-center gap-4 shrink-0">
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">
                                    Drive only
                                </span>

                                <form
                                    method="POST"
                                    action="{{ route('attachments.driveFileDestroy') }}"
                                    onsubmit="return confirm('Permanently delete this file from Google Drive? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="path" value="{{ $file['path'] }}">
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                        Delete from Drive
                                    </button>
                                </form>
                            </div>

                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-gray-500 text-sm">
                            Nothing untracked in this folder.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    @endforeach

</div>

@endsection
