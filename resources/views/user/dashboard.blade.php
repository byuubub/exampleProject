@extends('layouts.admin')

@section('title', 'My Dashboard')
@section('page-title', 'My Dashboard')

@section('content')

<div class="space-y-6 fade-in">

    {{-- Welcome --}}
    <div class="bg-gradient-to-r from-teal-500 to-cyan-500 rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-1">
                    Hello, {{ explode(' ', Auth::user()->full_name ?? 'User')[0] }}! 👋
                </h2>
                <p class="text-teal-100">
                    @if(Auth::user()->role === 'doctor')
                        {{ $stats['todayAppointments'] ?? 0 }} appointments scheduled for today
                    @else
                        Welcome to your health dashboard
                    @endif
                </p>
            </div>
            <div class="hidden sm:block">
                <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center">
                    <i class="fa-solid fa-user-circle text-white text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @if(Auth::user()->role === 'doctor')
        @foreach([
            ['label' => "Today's Appointments", 'value' => $stats['todayAppointments'] ?? 0, 'icon' => 'fa-calendar-day', 'color' => 'teal'],
            ['label' => 'Total Patients', 'value' => $stats['totalPatients'] ?? 0, 'icon' => 'fa-users', 'color' => 'blue'],
            ['label' => 'Pending', 'value' => $stats['pendingAppointments'] ?? 0, 'icon' => 'fa-clock', 'color' => 'amber'],
            ['label' => 'Completed', 'value' => $stats['completedAppointments'] ?? 0, 'icon' => 'fa-circle-check', 'color' => 'green'],
        ] as $stat)
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-{{ $stat['color'] }}-100 flex items-center justify-center mb-3">
                <i class="fa-solid {{ $stat['icon'] }} text-{{ $stat['color'] }}-500"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stat['value'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</p>
        </div>
        @endforeach
        @else
        @foreach([
            ['label' => 'Appointments', 'value' => $stats['totalAppointments'] ?? 0, 'icon' => 'fa-calendar-check', 'color' => 'teal'],
            ['label' => 'Medical Records', 'value' => $stats['totalRecords'] ?? 0, 'icon' => 'fa-file-medical', 'color' => 'blue'],
            ['label' => 'Unpaid Bills', 'value' => $stats['unpaidBills'] ?? 0, 'icon' => 'fa-receipt', 'color' => 'amber'],
            ['label' => 'Prescriptions', 'value' => $stats['activePrescriptions'] ?? 0, 'icon' => 'fa-prescription-bottle-medical', 'color' => 'green'],
        ] as $stat)
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-{{ $stat['color'] }}-100 flex items-center justify-center mb-3">
                <i class="fa-solid {{ $stat['icon'] }} text-{{ $stat['color'] }}-500"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stat['value'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</p>
        </div>
        @endforeach
        @endif
    </div>

    {{-- Recent Activities / Upcoming --}}
    <div class="grid lg:grid-cols-2 gap-6">

        {{-- Upcoming Appointments --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Upcoming Appointments</h3>
                <a href="{{ route('dashboard.appointments') }}" class="text-sm text-teal-600 hover:text-teal-700">View all</a>
            </div>
            @if($upcomingAppointments && $upcomingAppointments->count() > 0)
            <div class="space-y-3">
                @foreach($upcomingAppointments->take(5) as $appt)
                <div class="flex items-center space-x-4 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                    <div class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-calendar-day text-teal-500"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-900 dark:text-white text-sm">
                            @if(Auth::user()->role === 'doctor')
                                {{ $appt->patient->full_name ?? 'Patient' }}
                            @else
                                Dr. {{ $appt->doctor->full_name ?? 'Doctor' }}
                            @endif
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ \Carbon\Carbon::parse($appt->appointment_date)->format('D, d M Y') }} · {{ $appt->appointment_time }}
                        </p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                 {{ $appt->status === 'confirmed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ ucfirst($appt->status) }}
                    </span>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8 text-gray-400">
                <i class="fa-solid fa-calendar-xmark text-3xl mb-2"></i>
                <p>No upcoming appointments</p>
                @if(Auth::user()->role === 'patient')
                <a href="{{ route('hospitals.index') }}" class="mt-3 inline-block px-4 py-2 rounded-xl bg-teal-500 text-white text-sm font-medium">
                    Book an Appointment
                </a>
                @endif
            </div>
            @endif
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-3">
                @if(Auth::user()->role === 'patient')
                @foreach([
                    ['href' => route('hospitals.index'), 'icon' => 'fa-hospital', 'label' => 'Find Hospital', 'color' => 'teal'],
                    ['href' => route('dashboard.appointments'), 'icon' => 'fa-calendar-plus', 'label' => 'My Appointments', 'color' => 'blue'],
                    ['href' => route('dashboard.records'), 'icon' => 'fa-file-medical', 'label' => 'Medical Records', 'color' => 'purple'],
                    ['href' => route('dashboard.bills'), 'icon' => 'fa-receipt', 'label' => 'My Bills', 'color' => 'amber'],
                ] as $action)
                <a href="{{ $action['href'] }}"
                   class="flex flex-col items-center p-4 rounded-xl bg-{{ $action['color'] }}-50 hover:bg-{{ $action['color'] }}-100 transition-colors text-center">
                    <i class="fa-solid {{ $action['icon'] }} text-{{ $action['color'] }}-500 text-xl mb-2"></i>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $action['label'] }}</span>
                </a>
                @endforeach
                @elseif(Auth::user()->role === 'doctor')
                @foreach([
                    ['href' => route('dashboard.today'), 'icon' => 'fa-calendar-day', 'label' => "Today's Schedule", 'color' => 'teal'],
                    ['href' => route('dashboard.schedule'), 'icon' => 'fa-clock', 'label' => 'My Schedule', 'color' => 'blue'],
                    ['href' => route('dashboard.prescriptions'), 'icon' => 'fa-prescription', 'label' => 'Prescriptions', 'color' => 'purple'],
                    ['href' => route('dashboard.profile'), 'icon' => 'fa-user-doctor', 'label' => 'My Profile', 'color' => 'green'],
                ] as $action)
                <a href="{{ $action['href'] }}"
                   class="flex flex-col items-center p-4 rounded-xl bg-{{ $action['color'] }}-50 hover:bg-{{ $action['color'] }}-100 transition-colors text-center">
                    <i class="fa-solid {{ $action['icon'] }} text-{{ $action['color'] }}-500 text-xl mb-2"></i>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $action['label'] }}</span>
                </a>
                @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
