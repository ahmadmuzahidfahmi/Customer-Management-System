@extends('layouts.app')

@section('content')

<div x-data="{ view: localStorage.getItem('calendarView') || 'week' }"
     x-init="$watch('view', value => localStorage.setItem('calendarView', value))"
     class="space-y-6">

    <!-- Header -->
    <div class="flex justify-between items-center">

        <h1 class="text-2xl font-bold text-gray-800">
            Activity Calendar
        </h1>

        <div class="flex items-center gap-3">

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

            <a href="{{ route('calendar', [
                'month' => $prevMonth->month,
                'year' => $prevMonth->year
            ]) }}"
            class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">

                ← Previous

            </a>

            <div class="px-4 py-2 bg-white rounded-lg shadow">
                {{ $current->format('F Y') }}
            </div>

            <a href="{{ route('calendar', [
                'month' => $nextMonth->month,
                'year' => $nextMonth->year
            ]) }}"
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
                @endphp

                <div class="p-3 text-center">

                    <div class="font-semibold">
                        {{ $date->format('D') }}
                    </div>

                    <div class="text-sm text-gray-500">
                        {{ $date->format('d M') }}
                    </div>

                </div>

            @endfor

        </div>

        @for($hour = 8; $hour <= 18; $hour++)

            <div class="grid grid-cols-8 border-b min-h-[80px]">

                <div class="border-r p-2 text-sm text-gray-500">
                    {{ sprintf('%02d:00', $hour) }}
                </div>

                @for($day = 0; $day < 7; $day++)

                    @php
                        $date = $weekStart->copy()->addDays($day);

                        $slotActivities = $weeklyActivities->filter(function($activity) use ($date, $hour) {

                            if (!$activity->Dead_Line) {
                                return false;
                            }

                            return $activity->Dead_Line->isSameDay($date)
                                && $activity->Dead_Line->hour == $hour;
                        });
                    @endphp

                    <div class="border-r p-1">

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
                    $date = $current->copy()->day($day)->format('Y-m-d');
                    $dayActivities = $activities[$date] ?? collect();
                @endphp

                <div class="border min-h-[130px] p-2">

                    <!-- Day Number -->
                    <div class="font-semibold text-gray-800 mb-2">
                        {{ $day }}
                    </div>

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

</div>

@endsection
