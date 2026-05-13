@extends('layouts.admin')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')

<div class="space-y-6 fade-in">

    {{-- Date Filter --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.reports') }}" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">From</label>
                <input type="date" name="from" value="{{ request('from', now()->startOfMonth()->toDateString()) }}"
                       class="px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">To</label>
                <input type="date" name="to" value="{{ request('to', now()->toDateString()) }}"
                       class="px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none text-sm">
            </div>
            <button type="submit" class="px-4 py-2 rounded-xl bg-teal-500 text-white text-sm font-medium hover:bg-teal-600">
                <i class="fa-solid fa-filter mr-1"></i> Apply
            </button>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'Total Visits',    'value' => $totalVisits ?? 0,       'icon' => 'fa-chart-line',      'color' => 'teal'],
            ['label' => 'Total Revenue',   'value' => 'Rp '.number_format($totalRevenue ?? 0, 0, ',', '.'), 'icon' => 'fa-dollar-sign', 'color' => 'green'],
            ['label' => 'New Patients',    'value' => $newPatients ?? 0,       'icon' => 'fa-user-plus',       'color' => 'blue'],
            ['label' => 'Appointments',    'value' => $totalAppointments ?? 0, 'icon' => 'fa-calendar-check',  'color' => 'purple'],
        ] as $stat)
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-{{ $stat['color'] }}-100 dark:bg-{{ $stat['color'] }}-900/30 flex items-center justify-center mb-3">
                <i class="fa-solid {{ $stat['icon'] }} text-{{ $stat['color'] }}-500"></i>
            </div>
            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $stat['value'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Top Doctors --}}
    <div class="grid lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-6">Top Doctors by Visits</h3>
            @if(!empty($topDoctors))
            <div class="space-y-4">
                @foreach($topDoctors as $i => $doc)
                <div class="flex items-center space-x-4">
                    <div class="w-8 h-8 rounded-full bg-teal-100 text-teal-600 flex items-center justify-center text-sm font-bold flex-shrink-0">
                        {{ $i + 1 }}
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Dr. {{ $doc['name'] }}</span>
                            <span class="text-sm text-gray-500">{{ $doc['visits'] }} visits</span>
                        </div>
                        @php $max = $topDoctors[0]['visits'] ?? 1; @endphp
                        <div class="h-2 bg-gray-100 dark:bg-gray-700 rounded-full">
                            <div class="h-full bg-gradient-to-r from-teal-400 to-teal-500 rounded-full"
                                 style="width: {{ ($doc['visits'] / $max) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8 text-gray-400">
                <i class="fa-solid fa-chart-bar text-3xl mb-2"></i>
                <p>No data available</p>
            </div>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-6">Revenue by Hospital</h3>
            @if(!empty($revenueByHospital))
            <div class="space-y-4">
                @foreach($revenueByHospital as $item)
                @php $maxRev = $revenueByHospital[0]['revenue'] ?? 1; @endphp
                <div class="flex items-center space-x-4">
                    <div class="flex-1">
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $item['name'] }}</span>
                            <span class="text-sm text-gray-500">Rp {{ number_format($item['revenue'], 0, ',', '.') }}</span>
                        </div>
                        <div class="h-2 bg-gray-100 dark:bg-gray-700 rounded-full">
                            <div class="h-full bg-gradient-to-r from-cyan-400 to-cyan-500 rounded-full"
                                 style="width: {{ ($item['revenue'] / $maxRev) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8 text-gray-400">
                <i class="fa-solid fa-chart-bar text-3xl mb-2"></i>
                <p>No data available</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Appointments Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900 dark:text-white">Recent Appointments</h3>
            <a href="{{ route('admin.visits') }}" class="text-sm text-teal-600 hover:text-teal-700">View all visits →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Doctor</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Hospital</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($recentAppointments ?? [] as $appt)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                        <td class="px-6 py-3 text-sm text-gray-900 dark:text-white">{{ $appt->patient->full_name ?? 'N/A' }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-400 hidden md:table-cell">Dr. {{ $appt->doctor->user->full_name ?? 'N/A' }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-400 hidden lg:table-cell">{{ $appt->doctor->hospital->name ?? 'N/A' }}</td>
                        <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-400">{{ \Carbon\Carbon::parse($appt->appointment_date)->format('d M Y') }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                         {{ match($appt->status) {
                                             'completed'  => 'bg-green-100 text-green-700',
                                             'confirmed'  => 'bg-teal-100 text-teal-700',
                                             'cancelled'  => 'bg-red-100 text-red-700',
                                             default      => 'bg-blue-100 text-blue-700',
                                         } }}">
                                {{ ucfirst($appt->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">No appointments found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
