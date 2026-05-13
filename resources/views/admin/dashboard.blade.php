@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<div class="space-y-6 fade-in">

    {{-- Welcome Banner --}}
    <div class="bg-gradient-to-r from-teal-500 to-cyan-500 rounded-2xl p-6 text-white">
        <h2 class="text-2xl font-bold mb-2">
            Welcome back, {{ explode(' ', Auth::user()->full_name ?? 'User')[0] }}! 👋
        </h2>
        <p class="text-teal-100">Here's what's happening with your healthcare platform today.</p>
    </div>

    {{-- Main Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach([
            ['label' => 'Total Hospitals', 'value' => $stats['totalHospitals'] ?? 0, 'icon' => 'fa-hospital', 'color' => 'teal', 'trend' => '+12%'],
            ['label' => 'Total Users', 'value' => $stats['totalUsers'] ?? 0, 'icon' => 'fa-users', 'color' => 'blue', 'trend' => '+8%'],
            ['label' => 'Total Doctors', 'value' => $stats['totalDoctors'] ?? 0, 'icon' => 'fa-stethoscope', 'color' => 'purple', 'trend' => '+5%'],
            ['label' => 'Total Patients', 'value' => $stats['totalPatients'] ?? 0, 'icon' => 'fa-user-injured', 'color' => 'green', 'trend' => '+15%'],
        ] as $stat)
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-{{ $stat['color'] }}-100 dark:bg-{{ $stat['color'] }}-900/30 flex items-center justify-center">
                    <i class="fa-solid {{ $stat['icon'] }} text-{{ $stat['color'] }}-500 text-lg"></i>
                </div>
                <span class="flex items-center text-sm text-green-500 font-medium">
                    <i class="fa-solid fa-arrow-up-right mr-1 text-xs"></i>
                    {{ $stat['trend'] }}
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stat['value'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach([
            ['label' => "Today's Visits", 'value' => $stats['todayVisits'] ?? 0, 'icon' => 'fa-chart-line', 'color' => 'cyan'],
            ['label' => 'Monthly Revenue', 'value' => 'Rp '.number_format($stats['monthlyRevenue'] ?? 0, 0, ',', '.'), 'icon' => 'fa-dollar-sign', 'color' => 'amber'],
            ['label' => 'Active Queues', 'value' => $stats['activeQueues'] ?? 0, 'icon' => 'fa-activity', 'color' => 'rose'],
            ['label' => 'Pending Payments', 'value' => $stats['pendingPayments'] ?? 0, 'icon' => 'fa-clock', 'color' => 'red'],
        ] as $stat)
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-{{ $stat['color'] }}-100 dark:bg-{{ $stat['color'] }}-900/30 flex items-center justify-center">
                    <i class="fa-solid {{ $stat['icon'] }} text-{{ $stat['color'] }}-500"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $stat['value'] }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Charts --}}
    <div class="grid lg:grid-cols-2 gap-6">

        {{-- Revenue by Hospital --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Revenue by Hospital</h3>
            @if(!empty($stats['revenueByHospital']))
            <div class="space-y-4">
                @foreach(array_slice($stats['revenueByHospital'], 0, 5) as $item)
                @php $pct = $stats['monthlyRevenue'] > 0 ? min(($item['revenue'] / $stats['monthlyRevenue']) * 100, 100) : 0; @endphp
                <div class="flex items-center space-x-4">
                    <div class="flex-1">
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $item['name'] }}</span>
                            <span class="text-sm text-gray-500">Rp {{ number_format($item['revenue'], 0, ',', '.') }}</span>
                        </div>
                        <div class="h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-teal-400 to-teal-500 rounded-full"
                                 style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8 text-gray-400">
                <i class="fa-solid fa-chart-bar text-3xl mb-2"></i>
                <p>No revenue data available</p>
            </div>
            @endif
        </div>

        {{-- Visits by Month --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Visits per Month</h3>
            @if(!empty($stats['visitsByMonth']))
            @php $maxVisits = max(array_column($stats['visitsByMonth'], 'count')) ?: 1; @endphp
            <div class="flex items-end space-x-2 h-48">
                @foreach(array_reverse($stats['visitsByMonth']) as $item)
                @php $height = max(($item['count'] / $maxVisits) * 100, 5); @endphp
                <div class="flex-1 flex flex-col items-center">
                    <span class="text-xs text-gray-500 mb-1">{{ $item['count'] }}</span>
                    <div class="w-full bg-gradient-to-t from-cyan-400 to-cyan-500 rounded-t-lg"
                         style="height: {{ $height }}%"></div>
                    <span class="text-xs text-gray-500 mt-2">{{ $item['month'] }}</span>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8 text-gray-400">
                <i class="fa-solid fa-chart-column text-3xl mb-2"></i>
                <p>No visit data available</p>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection
