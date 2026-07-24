@extends('layouts.app')

@section('content')

<div class="bg-white rounded-lg shadow p-6">

    <h1 class="text-2xl font-bold mb-4">
        {{ $activity->Subject }}
    </h1>

    <div class="space-y-3">

        <p>
            <strong>Type:</strong>
            {{ $activity->Activity_Type }}
        </p>

        <p>
            <strong>Status:</strong>
            {{ $activity->Status }}
        </p>

        <p>
            <strong>Due Date:</strong>
            {{ $activity->Dead_Line?->format('d M Y') }}
        </p>

        <p>
            <strong>Due Time:</strong>
            {{ $activity->Dead_Line?->format('h:i A') }}
        </p>

        <p>
            <strong>Assigned To:</strong>
            {{ $activity->assignee->User_Name ?? 'Unassigned' }}
        </p>

        <p>
            <strong>Details:</strong><br>
            {{ $activity->Activity_Detail }}
        </p>

    </div>

</div>

@endsection