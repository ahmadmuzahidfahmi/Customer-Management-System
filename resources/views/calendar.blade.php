@extends('layouts.app')

@section('content')

<div x-data="{
        view: localStorage.getItem('calendarView') || 'week',
        creating: false,
        createDateTime: '',
        openCreate(dateTime) {
            this.createDateTime = dateTime;
            this.creating = true;
        }
     }"
     x-init="$watch('view', value => localStorage.setItem('calendarView', value))"
     class="space-y-6">

    <!-- Header -->
    <div class="flex justify-between items-center">

        <h1 class="text-2xl font-bold text-gray-800">
            Activity Calendar
        </h1>

        <div class="flex items-center gap-3">

            <button
                @click="openCreate('')"
                type="button"
                class="px-4 py-2 rounded-lg bg-cyan-600 text-white hover:bg-cyan-700 flex items-center gap-1">

                <span class="text-lg leading-none">+</span> New Activity

            </button>

            <button
                @click="view = 'week'"
                :class="view === 'week'
                    ? 'bg-cyan-600 text-white'
                    : 'bg-gray-200 text-gray-700'"
                class="px-4 py-2 rounded-lg">

                Weekly View

            </button>

            <button
                @click="view = 'month'"
                :class="view === 'month'
                    ? 'bg-cyan-600 text-white'
                    : 'bg-gray-200 text-gray-700'"
                class="px-4 py-2 rounded-lg">

                Monthly View

            </button>

            <!-- Adaptive Previous/Next: moves by week in Weekly View, by month in Monthly View -->

            <a x-show="view === 'month'"
               href="{{ route('calendar', [
                   'month' => $prevMonth->month,
                   'year' => $prevMonth->year,
                   'week' => request('week')
               ]) }}"
               class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">

                ← Previous

            </a>

            <a x-show="view === 'week'"
               href="{{ route('calendar', ['week' => $prevWeek, 'month' => $current->month, 'year' => $current->year]) }}"
               class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">

                ← Previous

            </a>

            <div class="px-4 py-2 bg-white rounded-lg shadow">
                <span x-show="view === 'month'">{{ $current->format('F Y') }}</span>
                <span x-show="view === 'week'">{{ $weekStart->format('d M') }} – {{ $weekEnd->format('d M Y') }}</span>
            </div>

            <a x-show="view === 'month'"
               href="{{ route('calendar', [
                   'month' => $nextMonth->month,
                   'year' => $nextMonth->year,
                   'week' => request('week')
               ]) }}"
               class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">

                Next →

            </a>

            <a x-show="view === 'week'"
               href="{{ route('calendar', ['week' => $nextWeek, 'month' => $current->month, 'year' => $current->year]) }}"
               class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">

                Next →

            </a>

        </div>

    </div>

    <!-- Weekly Calendar -->
    <div x-show="view === 'week'"
         class="bg-white rounded-lg shadow overflow-hidden">

        <div class="grid grid-cols-8 border-b bg-gray-50">

            <div class="p-3 font-semibold text-gray-600">
                Time
            </div>

            @for($i = 0; $i < 7; $i++)

                @php
                    $date = $weekStart->copy()->addDays($i);
                    $isToday = $date->isToday();
                @endphp

                <div class="p-3 text-center {{ $isToday ? 'bg-cyan-100' : '' }}">

                    <div class="font-semibold {{ $isToday ? 'text-cyan-700' : '' }}">
                        {{ $date->format('D') }}
                    </div>

                    <div class="text-sm {{ $isToday ? 'text-cyan-600' : 'text-gray-500' }}">
                        {{ $date->format('d M') }}
                    </div>

                </div>

            @endfor

        </div>

        @for($hour = 0; $hour <= 23; $hour++)

            <div class="grid grid-cols-8 border-b min-h-[80px]">

                <div class="border-r p-2 text-sm text-gray-500">
                    @php
                        $displayHour = $hour % 12;
                        if ($displayHour === 0) {
                            $displayHour = 12;
                        }
                        $ampm = $hour < 12 ? 'AM' : 'PM';
                    @endphp
                    {{ $displayHour }}:00 {{ $ampm }}
                </div>

                @for($day = 0; $day < 7; $day++)

                    @php
                        $date = $weekStart->copy()->addDays($day);
                        $isToday = $date->isToday();

                        $slotActivities = $weeklyActivities->filter(function($activity) use ($date, $hour) {

                            if (!$activity->Dead_Line) {
                                return false;
                            }

                            return $activity->Dead_Line->isSameDay($date)
                                && $activity->Dead_Line->hour == $hour;
                        });
                    @endphp

                    <div class="border-r p-1 {{ $isToday ? 'bg-cyan-50' : '' }} hover:bg-gray-50 cursor-pointer"
                         @click.self="openCreate('{{ $date->format('Y-m-d') }}T{{ sprintf('%02d', $hour) }}:00')">

                        @foreach($slotActivities as $activity)

                            <a href="{{ route('activities.show', $activity->Activity_ID) }}"
                               class="block bg-cyan-100 text-cyan-700 rounded px-2 py-1 text-xs mb-1">

                                {{ $activity->Subject }}

                            </a>

                        @endforeach

                    </div>

                @endfor

            </div>

        @endfor

    </div>

    <!-- Monthly Calendar -->
    <div x-show="view === 'month'"
         class="bg-white rounded-lg shadow overflow-hidden">

        <!-- Days Header -->
        <div class="grid grid-cols-7 bg-gray-100">

            @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)

                <div class="p-3 text-center font-semibold text-gray-700">
                    {{ $day }}
                </div>

            @endforeach

        </div>

        <!-- Calendar Grid -->
        <div class="grid grid-cols-7">

            @php
                $startDay = $current->copy()->startOfMonth()->dayOfWeek;
                $daysInMonth = $current->daysInMonth;
            @endphp

            {{-- Empty cells before month start --}}
            @for($i = 0; $i < $startDay; $i++)
                <div class="border min-h-[130px] bg-gray-50"></div>
            @endfor

            {{-- Days --}}
            @for($day = 1; $day <= $daysInMonth; $day++)

                @php
                    $dateCarbon = $current->copy()->day($day);
                    $date = $dateCarbon->format('Y-m-d');
                    $dayActivities = $activities[$date] ?? collect();
                    $isToday = $dateCarbon->isToday();
                @endphp

                <div class="border min-h-[130px] p-2 group relative {{ $isToday ? 'bg-cyan-50 border-cyan-400 border-2' : '' }}">

                    <!-- Day Number -->
                    <div class="font-semibold mb-2 {{ $isToday ? 'text-cyan-700' : 'text-gray-800' }}">
                        {{ $day }}
                        @if($isToday)
                            <span class="ml-1 text-xs font-normal bg-cyan-600 text-white px-1.5 py-0.5 rounded">Today</span>
                        @endif
                    </div>

                    <!-- Quick Add -->
                    <button
                        type="button"
                        @click="openCreate('{{ $dateCarbon->format('Y-m-d') }}T09:00')"
                        class="absolute top-2 right-2 w-5 h-5 flex items-center justify-center rounded-full bg-gray-200 text-gray-600 opacity-0 group-hover:opacity-100 hover:bg-cyan-600 hover:text-white transition text-sm leading-none">
                        +
                    </button>

                    <!-- Activities -->
                    <div class="space-y-1">

                        @foreach($dayActivities->take(3) as $activity)

                            @php

                                if ($activity->isOverdue()) {
                                    $color = 'bg-red-100 text-red-700';
                                } elseif ($activity->Status === 'Completed') {
                                    $color = 'bg-green-100 text-green-700';
                                } else {
                                    $color = 'bg-blue-100 text-blue-700';
                                }

                            @endphp

                            <a href="{{ route('activities.show', $activity->Activity_ID) }}"
                               class="block text-xs px-2 py-1 rounded {{ $color }} truncate">

                                {{ $activity->Subject }}

                            </a>

                        @endforeach

                        @if($dayActivities->count() > 3)

                            <div class="text-xs text-gray-500">
                                +{{ $dayActivities->count() - 3 }} more
                            </div>

                        @endif

                    </div>

                </div>

            @endfor

        </div>

    </div>

    <!-- New Activity Modal -->
    <div x-show="creating"
         x-cloak
         @click.self="creating = false"
         @keydown.escape.window="creating = false"
         class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">

        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">

            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">New Activity</h2>
                <button @click="creating = false" type="button" class="text-gray-400 hover:text-gray-600 text-lg leading-none">✕</button>
            </div>

            <form method="POST" action="{{ route('activities.store') }}" class="space-y-2">
                @csrf

                <select name="Activity_Type" required class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="">Type...</option>
                    @foreach(['Call','Email','Meeting','Follow-Up','Other'] as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>

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

                <div class="grid grid-cols-2 gap-2">

                    <select name="Lead_ID" class="w-full border rounded-lg px-3 py-2 text-sm">
                        <option value="">Link to Lead...</option>
                        @foreach($leads as $lead)
                            <option value="{{ $lead->Lead_ID }}">{{ $lead->Lead_Name }}</option>
                        @endforeach
                    </select>

                    <select name="Contact_ID" class="w-full border rounded-lg px-3 py-2 text-sm">
                        <option value="">Link to Contact...</option>
                        @foreach($contacts as $contact)
                            <option value="{{ $contact->Contact_ID }}">{{ $contact->Contact_Name }}</option>
                        @endforeach
                    </select>

                </div>

                <p class="text-xs text-gray-500">Must link to at least a Lead or a Contact.</p>

                <input
                    type="datetime-local"
                    name="Dead_Line"
                    x-bind:value="createDateTime"
                    class="w-full border rounded-lg px-3 py-2 text-sm">

                <select name="Assigned_To" class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="">Assign to me</option>
                    @foreach($users as $u)
                        <option value="{{ $u->User_ID }}">{{ $u->User_Name }}</option>
                    @endforeach
                </select>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="creating = false" class="px-4 py-2 rounded-lg bg-gray-200 text-sm">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-cyan-600 text-white text-sm hover:bg-cyan-700">
                        Create Activity
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>

@endsection