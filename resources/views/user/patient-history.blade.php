@extends('layouts.admin')
@section('title', 'Patient History')
@section('page-title', 'Patient History')

@section('content')
<div class="space-y-6 fade-in">

    {{-- Patient Header --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center space-x-5">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-teal-400 to-cyan-500 flex items-center justify-center text-2xl font-bold text-white flex-shrink-0">
                {{ strtoupper(substr($patient->user->full_name ?? 'P', 0, 1)) }}
            </div>
            <div class="flex-1">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $patient->user->full_name ?? 'Patient' }}</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $patient->user->email ?? '' }}</p>
                <div class="flex flex-wrap gap-3 mt-2">
                    @if($patient->medical_record_number)
                    <span class="text-xs px-2.5 py-1 rounded-full bg-teal-100 text-teal-700">MRN: {{ $patient->medical_record_number }}</span>
                    @endif
                    @if($patient->blood_type)
                    <span class="text-xs px-2.5 py-1 rounded-full bg-red-100 text-red-700">Blood: {{ $patient->blood_type }}</span>
                    @endif
                    @if($patient->gender)
                    <span class="text-xs px-2.5 py-1 rounded-full bg-blue-100 text-blue-700">{{ ucfirst($patient->gender) }}</span>
                    @endif
                    @if($patient->date_of_birth)
                    <span class="text-xs px-2.5 py-1 rounded-full bg-gray-100 text-gray-600">
                        {{ $patient->date_of_birth->format('d M Y') }} ({{ $patient->date_of_birth->age }} yrs)
                    </span>
                    @endif
                </div>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold text-teal-500">{{ $appointments->count() }}</p>
                <p class="text-xs text-gray-500">Total Visits</p>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">

        {{-- Medical Records --}}
        <div class="lg:col-span-2 space-y-4">
            <h3 class="font-semibold text-gray-900 dark:text-white">Medical Records</h3>

            @forelse($records as $record)
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm" x-data="{ open: false }">
                <button @click="open = !open" class="w-full flex items-start justify-between text-left">
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">{{ Str::limit($record->diagnosis, 60) }}</p>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $record->created_at->format('d M Y') }} · Dr. {{ $record->doctor->user->full_name ?? 'N/A' }}</p>
                    </div>
                    <i class="fa-solid fa-chevron-down text-gray-400 mt-1 transition-transform flex-shrink-0 ml-4" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open" x-cloak class="mt-4 pt-4 border-t dark:border-gray-700 space-y-3 text-sm">
                    @if($record->treatment_plan)
                    <div><span class="font-medium text-gray-500">Treatment:</span> <span class="text-gray-700 dark:text-gray-300">{{ $record->treatment_plan }}</span></div>
                    @endif
                    @if($record->notes)
                    <div><span class="font-medium text-gray-500">Notes:</span> <span class="text-gray-700 dark:text-gray-300">{{ $record->notes }}</span></div>
                    @endif
                    @if($record->prescriptions->count())
                    <div>
                        <span class="font-medium text-gray-500">Prescriptions:</span>
                        <ul class="mt-1 space-y-1">
                            @foreach($record->prescriptions as $rx)
                            <li class="flex items-center text-gray-700 dark:text-gray-300">
                                <i class="fa-solid fa-pills text-teal-400 mr-2 text-xs"></i>
                                {{ $rx->medicine_name }} — {{ $rx->dosage }} {{ $rx->frequency }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="bg-white dark:bg-gray-800 rounded-2xl py-12 text-center text-gray-400 shadow-sm">
                <i class="fa-solid fa-file-medical text-3xl mb-2"></i>
                <p>No medical records yet</p>
            </div>
            @endforelse
        </div>

        {{-- Visit History Sidebar --}}
        <div>
            <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Visit History</h3>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm space-y-3 max-h-[600px] overflow-y-auto">
                @forelse($appointments as $appt)
                <div class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/30">
                    <div class="w-2 h-2 rounded-full flex-shrink-0
                                {{ $appt->status === 'completed' ? 'bg-green-500' : ($appt->status === 'cancelled' ? 'bg-red-400' : 'bg-amber-400') }}"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $appt->appointment_date->format('d M Y') }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ Str::limit($appt->complaint, 35) }}</p>
                    </div>
                    <span class="text-xs text-gray-400">{{ ucfirst($appt->status) }}</span>
                </div>
                @empty
                <p class="text-center text-gray-400 text-sm py-4">No visits</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
