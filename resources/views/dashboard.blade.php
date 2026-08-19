<head> 
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        Dashboard
    </h1>

    <div x-data="{
    sections: JSON.parse(localStorage.getItem('dashboardSections') ?? '{}'),
    isCollapsed(key) { return this.sections[key] === true; },
    toggleSection(key) {
        this.sections[key] = !this.isCollapsed(key);
        localStorage.setItem('dashboardSections', JSON.stringify(this.sections));
    }
}">

@if($pinnedCustomers->count() || $pinnedLeads->count())
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">📌 Pinned</h2>
            <button type="button" @click="toggleSection('pinned')" class="text-sm text-gray-500 hover:text-gray-700">
                <span x-text="isCollapsed('pinned') ? '▸ Show' : '▾ Hide'"></span>
            </button>
        </div>

        <div x-show="!isCollapsed('pinned')" x-cloak class="space-y-6">

            @if($pinnedCustomers->count())
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Customers</p>
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">Company</th>
                                    <th class="px-6 py-3 text-left">Email</th>
                                    <th class="px-6 py-3 text-left">Phone</th>
                                    <th class="px-6 py-3 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($pinnedCustomers as $customer)
                                    <tr
                                        onclick="window.location='{{ route('customers.show', $customer->Company_ID) }}'"
                                        class="cursor-pointer hover:bg-cyan-50">
                                        <td class="px-6 py-4">{{ $customer->Company_Name }}</td>
                                        <td class="px-6 py-4">{{ $customer->Company_Email }}</td>
                                        <td class="px-6 py-4">{{ $customer->Company_No ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">
                                            @if($customer->Status == 'Active')
                                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Active</span>
                                            @elseif($customer->Status == 'Lead')
                                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">Lead</span>
                                            @else
                                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

                        @if($pinnedContacts->count())
    <div>
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Contacts</p>
    <div class="overflow-x-auto border rounded-lg">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left">Name</th>
                <th class="px-6 py-3 text-left">Company</th>
                <th class="px-6 py-3 text-left">Email</th>
                <th class="px-6 py-3 text-left">Phone</th>
                <th class="px-6 py-3 text-left">Position</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-100">

            @foreach($pinnedContacts as $contact)

            <tr
                onclick="window.location='{{ route('contacts.show', $contact->Contact_ID) }}'"
                class="cursor-pointer hover:bg-cyan-50">

                <td class="px-6 py-4">

                    <div class="flex items-center gap-2 group">

                        <span class="font-medium">
                        {{ $contact->Contact_Name }}
                        </span>

                        <form
                            action="{{ route('contacts.pin', $contact->Contact_ID) }}"
                            method="POST"
                            onclick="event.stopPropagation()">

                            @csrf

                        </form>

                    </div>

                <td class="px-6 py-4"> {{ $contact->company->Company_Name ?? 'N/A' }}</td>

                </td>

                <td class="px-6 py-4">
                    {{ $contact->Contact_Email }}
                </td>

                <td class="px-6 py-4">
                    {{ $contact->Country_Code }}
                    {{ $contact->Contact_No }}
                </td>

                <td class="px-6 py-4">
                    {{ $contact->Contact_Role }}
                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

</div>
</div>
            @endif

            @if($pinnedLeads->count())
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Leads</p>
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">Lead Name</th>
                                    <th class="px-6 py-3 text-left">Customer</th>
                                    <th class="px-6 py-3 text-left">Value</th>
                                    <th class="px-6 py-3 text-left">Source</th>
                                    <th class="px-6 py-3 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($pinnedLeads as $lead)
                                    <tr
                                        onclick="window.location='{{ route('leads.show', $lead->Lead_ID) }}'"
                                        class="cursor-pointer hover:bg-cyan-50">
                                        <td class="px-6 py-4">{{ $lead->Lead_Name }}</td>
                                        <td class="px-6 py-4">{{ $lead->company->Company_Name ?? 'No Company' }}</td>
                                        <td class="px-6 py-4">{{ $lead->Estimated_Value ?? 'unknown' }}</td>
                                        <td class="px-6 py-4">{{ $lead->Source ?? 'unknown' }}</td>
                                        <td class="px-6 py-4">
                                            @if($lead->Status == 'New')
                                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">New</span>
                                            @elseif($lead->Status == 'Won')
                                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Won</span>
                                            @elseif($lead->Status == 'Qualified')
                                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">Qualified</span>
                                            @elseif($lead->Status == 'Contacted')
                                                <span class="px-2 py-1 text-xs rounded-full bg-amber-100 text-amber-700">Contacted</span>
                                            @else
                                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">Lost</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
    @endif

    <!-- Summary Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <!-- Customers -->
    <a href="{{ route('customers') }}"
       class="block bg-white rounded-lg shadow p-6 hover:shadow-lg hover:-translate-y-1 transition duration-200">

        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm text-gray-500">Total Customers</p>
                <p class="text-3xl font-bold text-indigo-600">
                    {{ $totalCustomers }}
                </p>
            </div>

            <span class="text-2xl text-gray-400">→</span>
        </div>

    </a>

    <!-- Leads -->
    <a href="{{ route('leads') }}"
       class="block bg-white rounded-lg shadow p-6 hover:shadow-lg hover:-translate-y-1 transition duration-200">

        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm text-gray-500">Total Leads</p>
                <p class="text-3xl font-bold text-green-600">
                    {{ $totalLeads }}
                </p>
            </div>

            <span class="text-2xl text-gray-400">→</span>
        </div>

    </a>

    <!-- Won Leads -->
    <a href="{{ route('leads') }}?status=Won"
       class="block bg-white rounded-lg shadow p-6 hover:shadow-lg hover:-translate-y-1 transition duration-200">

        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm text-gray-500">Won Leads</p>
                <p class="text-3xl font-bold text-emerald-600">
                    {{ $wonLeads }}
                </p>
            </div>

            <span class="text-2xl text-gray-400">→</span>
        </div>

    </a>

    <!-- Lost Leads -->
    <a href="{{ route('leads') }}?status=Lost"
       class="block bg-white rounded-lg shadow p-6 hover:shadow-lg hover:-translate-y-1 transition duration-200">

        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm text-gray-500">Lost Leads</p>
                <p class="text-3xl font-bold text-red-600">
                    {{ $lostLeads }}
                </p>
            </div>

            <span class="text-2xl text-gray-400">→</span>
        </div>

    </a>

</div>

<!-- Activity Summary -->
        <h2 class="text-lg font-semibold text-gray-800 mb-3">
        Activity Summary
        </h2>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

    <!-- Due Today -->
    
    <a href="{{ route('activities.index', ['filter' => 'today']) }}"
    class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500 hover:shadow-lg transition block">

        <p class="text-sm text-gray-500">
            Due Today
        </p>

        <p class="text-3xl font-bold text-yellow-600">
            {{ $dueToday }}
        </p>

    </a>

    <!-- Overdue -->
    <a href="{{ route('activities.index', ['filter' => 'overdue']) }}"
    class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500 hover:shadow-lg transition block">

        <p class="text-sm text-gray-500">
            Overdue
        </p>

        <p class="text-3xl font-bold text-red-600">
            {{ $overdueActivities }}
        </p>

    </a>

    <!-- Completed -->
    <a href="{{ route('activities.index', ['filter' => 'completed']) }}"
    class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500 hover:shadow-lg transition block">

        <p class="text-sm text-gray-500">
            Completed This Week
        </p>

        <p class="text-3xl font-bold text-green-600">
            {{ $completedThisWeek }}
        </p>

    </a>

</div>

    <!-- Customer Growth Graph -->
<div
    class="bg-white rounded-lg shadow p-6 mb-6"
    x-data="{ chartView: 'month' }">

    <div class="flex justify-between items-center mb-4">

        <h2 class="text-lg font-semibold text-gray-800">
            Customer Growth
        </h2>

<div class="flex flex-col items-end">

    <div class="flex gap-2 bg-gray-100 p-1 rounded-lg">

        <!-- Monthly -->
        <button
            @click="chartView = 'month'; updateChart('month')"
            :class="chartView === 'month'
                ? 'bg-cyan-600 text-white shadow'
                : 'text-gray-600 hover:bg-gray-200'"
            class="px-4 py-2 rounded-md text-sm font-medium transition-all duration-200">
            Monthly
        </button>

        <!-- Weekly -->
        <button
            @click="chartView = 'week'; updateChart('week')"
            :class="chartView === 'week'
                ? 'bg-cyan-600 text-white shadow'
                : 'text-gray-600 hover:bg-gray-200'"
            class="px-4 py-2 rounded-md text-sm font-medium transition-all duration-200">
            Weekly
        </button>

    </div>

    <p class="text-xs text-gray-500 mt-2">
        Viewing:
        <span class="font-semibold text-cyan-600"
              x-text="chartView === 'month' ? 'Monthly Data' : 'Weekly Data'">
        </span>
    </p>

</div>

    </div>

    <div class="h-64">
        <canvas id="customerGrowthChart"></canvas>
    </div>

</div>

<!-- Recent Customers & Upcoming Follow-Ups -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- Recent Customers -->
<div class="bg-white rounded-lg shadow">

    <div class="px-6 py-4 border-b flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800">
            Recent Customers
        </h2>

        <a href="{{ route('customers') }}"
           class="text-cyan-600 hover:text-cyan-700 text-sm font-medium">
            View All
        </a>
    </div>

    <div class="divide-y divide-gray-200">

        @forelse($recentCustomers as $customer)

            <div class="px-6 py-4 flex justify-between items-center">

                <div>
                    <p class="font-medium text-gray-800">
                        {{ $customer->Company_Name }}
                    </p>

                    <p class="text-sm text-gray-500">
                        {{ $customer->Status ?? 'Active' }}
                    </p>
                </div>

                <div class="text-right">
                    <p class="text-sm text-gray-500">
                        {{ $customer->Closed_Date?->format('d M Y') ?? 'N/A' }}                    </p>

                    <p class="text-xs text-gray-400">
                        Added
                    </p>
                </div>

            </div>

        @empty

            <div class="px-6 py-8 text-center text-gray-500">
                No customers found.
            </div>

        @endforelse

    </div>

</div>

<!-- Upcoming Follow-Ups -->
<div class="bg-white rounded-lg shadow p-6 mb-3">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">
        Upcoming Follow-Ups
    </h2>

    <div class="space-y-3">
        @forelse($upcomingFollowUps as $followUp)
            @php
                $borderColor = $followUp->isOverdue()
                    ? 'border-red-500'
                    : ($followUp->Dead_Line->isToday() ? 'border-yellow-500' : 'border-blue-500');
            @endphp

            <div class="border-l-4 {{ $borderColor }} pl-3">
                <p class="font-medium">
                    {{ $followUp->lead->Lead_Name ?? $followUp->contact->Contact_Name ?? 'Unlinked' }}
                </p>
                <p class="text-sm text-gray-500">
                    {{ $followUp->Subject }} —
                    {{ $followUp->Dead_Line->isToday() ? 'Today' : $followUp->Dead_Line->format('d M') }}
                </p>
            </div>
        @empty
            <p class="text-sm text-gray-500">No upcoming follow-ups.</p>
        @endforelse
    </div>
</div>

<!-- Recent Activity Feed -->
<div class="bg-white rounded-lg shadow p-6 col-span-1 lg:col-span-2">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">
        Recent Activity
    </h2>

    <div class="space-y-4">
        @forelse($recentActivities as $activity)
            @php
                $dotColor = $activity->Status === 'Completed'
                    ? 'bg-green-500'
                    : ($activity->Status === 'Cancelled' ? 'bg-gray-400' : 'bg-blue-500');
            @endphp

            <div class="flex items-start space-x-3">
                <div class="w-2 h-2 mt-2 rounded-full {{ $dotColor }}"></div>
                <div>
                    <p class="text-sm text-gray-800">
                        <span class="font-semibold">{{ $activity->creator->User_Name ?? 'Someone' }}</span>
                        logged a {{ strtolower($activity->Activity_Type) }}
                        @if($activity->lead)
                            for <span class="font-medium">{{ $activity->lead->Lead_Name }}</span>
                        @elseif($activity->contact)
                            with <span class="font-medium">{{ $activity->contact->Contact_Name }}</span>
                        @endif
                        — {{ $activity->Subject }}
                    </p>
                    <p class="text-xs text-gray-500">{{ $activity->Created_At->diffForHumans() }}</p>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-500">No recent activity.</p>
        @endforelse
    </div>
</div>


@push('scripts')
<script>

window.monthLabels = @json($growthLabels);
window.monthData = @json($growthData);

window.weekLabels = @json($weeklyLabels);
window.weekData = @json($weeklyData);

let customerChart;

document.addEventListener('DOMContentLoaded', function () {

    const ctx = document.getElementById('customerGrowthChart');

    customerChart = new Chart(ctx, {

        type: 'line',

        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Customers',
                data: monthData,
                borderColor: 'rgb(70,192,189)',
                backgroundColor: 'rgba(70,192,189,0.1)',
                fill: true,
                tension: 0.3
            }]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
});

window.updateChart = function(view) {

    if (view === 'week') {

        customerChart.data.labels = weekLabels;
        customerChart.data.datasets[0].data = weekData;

    } else {

        customerChart.data.labels = monthLabels;
        customerChart.data.datasets[0].data = monthData;
    }

    customerChart.update();
};


</script>
@endpush
</div>
@endsection