<head> 
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        Dashboard
    </h1>

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

    <!-- Customer Growth Graph -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">
            Customer Growth
        </h2>

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
                        {{ $customer->Closed_Date->format('d M Y') }}
                    </p>

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
</div>

@push('scripts')
<script type="module">
    import Chart from 'chart.js/auto';

    new Chart(document.getElementById('customerGrowthChart'), {
        type: 'line',
        data: {
            labels: @json($growthLabels),
            datasets: [{
                label: 'Total Customers',
                data: @json($growthData),
                borderColor: 'rgb(70, 192, 189)',
                backgroundColor: 'rgba(70, 192, 189, 0.1)',
                tension: 0.3,
                fill: true,
                pointRadius: 3,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });
</script>
@endpush
    
@endsection