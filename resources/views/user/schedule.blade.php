@extends('layouts.admin')
@section('title', 'My Schedule')
@section('page-title', 'My Schedule')

@section('content')
<div class="space-y-6 fade-in">

    {{-- Weekly Schedule --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm">
        <h3 class="font-semibold text-gray-900 dark:text-white mb-6">Practice Schedule</h3>

        @php $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday']; @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
            @foreach($days as $day)
            @php $daySchedule = $schedules->where('day_of_week', $day)->first(); @endphp
            <div class="rounded-xl p-3 text-center
                        {{ $daySchedule && $daySchedule->is_active
                           ? 'bg-teal-50 dark:bg-teal-900/30 border-2 border-teal-200 dark:border-teal-800'
                           : 'bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600' }}">
                <p class="text-xs font-semibold {{ $daySchedule && $daySchedule->is_active ? 'text-teal-700 dark:text-teal-400' : 'text-gray-400' }} mb-2">
                    {{ substr($day, 0, 3) }}
                </p>
                @if($daySchedule && $daySchedule->is_active)
                    <p class="text-xs font-medium text-teal-600 dark:text-teal-400">{{ $daySchedule->start_time }}</p>
                    <p class="text-xs text-teal-400">to</p>
                    <p class="text-xs font-medium text-teal-600 dark:text-teal-400">{{ $daySchedule->end_time }}</p>
                    @if($daySchedule->quota)
                    <p class="text-xs text-gray-500 mt-1">{{ $daySchedule->quota }}pts</p>
                    @endif
                @else
                    <p class="text-xs text-gray-300 dark:text-gray-600">Off</p>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- Upcoming Appointments This Week --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm">
        <h3 class="font-semibold text-gray-900 dark:text-white mb-6">Appointments This Week</h3>

        @if($weekAppointments->isEmpty())
        <div class="py-10 text-center text-gray-400">
            <i class="fa-solid fa-calendar-xmark text-4xl mb-3"></i>
            <p>No appointments this week</p>
        </div>
        @else
        <div class="space-y-6">
            @foreach($weekAppointments as $date => $appts)
            <div>
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-3">
                    {{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}
                    @if($date === today()->toDateString())
                    <span class="ml-2 px-2 py-0.5 rounded-full text-xs bg-teal-100 text-teal-700">Today</span>
                    @endif
                </p>
                <div class="space-y-2">
                    @foreach($appts as $appt)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center">
                                <i class="fa-solid fa-user text-teal-500 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $appt->patient->user->full_name ?? 'Patient' }}</p>
                                <p class="text-xs text-gray-500">{{ $appt->appointment_time }} · {{ Str::limit($appt->complaint, 40) }}</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                     {{ match($appt->status) {
                                         'confirmed'   => 'bg-teal-100 text-teal-700',
                                         'completed'   => 'bg-green-100 text-green-700',
                                         'in_progress' => 'bg-blue-100 text-blue-700',
                                         'cancelled'   => 'bg-red-100 text-red-700',
                                         default       => 'bg-gray-100 text-gray-600',
                                     } }}">
                            {{ ucfirst(str_replace('_', ' ', $appt->status)) }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
