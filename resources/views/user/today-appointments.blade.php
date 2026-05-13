@extends('layouts.admin')
@section('title', "Today's Appointments")
@section('page-title', "Today's Appointments")

@section('content')
<div class="space-y-6 fade-in">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Today's Patients</h2>
            <p class="text-gray-500 dark:text-gray-400">{{ now()->format('l, d F Y') }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl px-4 py-2.5 shadow-sm text-center">
            <p class="text-2xl font-bold text-teal-500">{{ $appointments->total() }}</p>
            <p class="text-xs text-gray-500">Total</p>
        </div>
    </div>

    {{-- Appointments --}}
    <div class="space-y-3">
        @forelse($appointments as $appt)
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex items-center space-x-4 flex-1">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-teal-100 to-cyan-100 flex items-center justify-center flex-shrink-0 text-lg font-bold text-teal-600">
                        {{ strtoupper(substr($appt->patient->user->full_name ?? 'P', 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $appt->patient->user->full_name ?? 'Patient' }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            <i class="fa-solid fa-clock mr-1 text-teal-400"></i>{{ $appt->appointment_time }}
                            @if($appt->complaint)
                            · {{ Str::limit($appt->complaint, 50) }}
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                 {{ match($appt->status) {
                                     'confirmed'   => 'bg-teal-100 text-teal-700',
                                     'in_progress' => 'bg-blue-100 text-blue-700',
                                     'completed'   => 'bg-green-100 text-green-700',
                                     'cancelled'   => 'bg-red-100 text-red-700',
                                     default       => 'bg-gray-100 text-gray-600',
                                 } }}">
                        {{ ucfirst(str_replace('_', ' ', $appt->status)) }}
                    </span>

                    @if(in_array($appt->status, ['confirmed', 'in_progress']))
                    <a href="{{ route('dashboard.examination', $appt->id) }}"
                       class="inline-flex items-center px-3 py-1.5 rounded-lg bg-teal-500 text-white text-sm font-medium hover:bg-teal-600 transition-colors">
                        <i class="fa-solid fa-stethoscope mr-1.5"></i> Examine
                    </a>
                    @endif

                    <a href="{{ route('dashboard.patient.history', $appt->patient_id) }}"
                       class="p-1.5 rounded-lg text-gray-400 hover:text-teal-600 hover:bg-teal-50 dark:hover:bg-teal-900/30 transition-colors"
                       title="View History">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white dark:bg-gray-800 rounded-2xl py-20 text-center text-gray-400 shadow-sm">
            <i class="fa-solid fa-calendar-check text-5xl mb-4"></i>
            <p class="text-lg font-medium">No appointments today</p>
            <p class="text-sm mt-1">Enjoy your day off!</p>
        </div>
        @endforelse
    </div>

    @if($appointments->hasPages())
    <div>{{ $appointments->links() }}</div>
    @endif
</div>
@endsection
