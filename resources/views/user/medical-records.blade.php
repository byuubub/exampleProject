@extends('layouts.admin')
@section('title', 'Medical Records')
@section('page-title', 'Medical Records')

@section('content')
<div class="space-y-6 fade-in">

    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Medical Records</h2>
        <p class="text-gray-500 dark:text-gray-400">Your complete health history</p>
    </div>

    @forelse($records as $record)
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden" x-data="{ open: false }">
        {{-- Header --}}
        <button @click="open = !open" class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-file-medical text-teal-500 text-lg"></i>
                </div>
                <div class="text-left">
                    <p class="font-semibold text-gray-900 dark:text-white">{{ Str::limit($record->diagnosis, 60) }}</p>
                    <div class="flex items-center space-x-3 text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                        <span>Dr. {{ $record->doctor->user->full_name ?? 'N/A' }}</span>
                        <span>·</span>
                        <span>{{ $record->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <span class="px-2.5 py-1 rounded-full text-xs font-medium hidden sm:inline-block
                             {{ match($record->case_status) {
                                 'resolved'  => 'bg-green-100 text-green-700',
                                 'follow_up' => 'bg-amber-100 text-amber-700',
                                 default     => 'bg-blue-100 text-blue-700',
                             } }}">
                    {{ ucfirst(str_replace('_', ' ', $record->case_status)) }}
                </span>
                <i class="fa-solid fa-chevron-down text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''"></i>
            </div>
        </button>

        {{-- Expanded Content --}}
        <div x-show="open" x-cloak class="border-t dark:border-gray-700 p-6 space-y-5">
            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Diagnosis</p>
                    <p class="text-gray-700 dark:text-gray-300">{{ $record->diagnosis }}</p>
                </div>
                @if($record->treatment_plan)
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Treatment Plan</p>
                    <p class="text-gray-700 dark:text-gray-300">{{ $record->treatment_plan }}</p>
                </div>
                @endif
                @if($record->notes)
                <div class="md:col-span-2">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Doctor's Notes</p>
                    <p class="text-gray-700 dark:text-gray-300">{{ $record->notes }}</p>
                </div>
                @endif
            </div>

            @if($record->prescriptions->count() > 0)
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Prescriptions</p>
                <div class="space-y-2">
                    @foreach($record->prescriptions as $rx)
                    <div class="flex items-start space-x-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                        <div class="w-8 h-8 rounded-lg bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-pills text-teal-500 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $rx->medicine_name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $rx->dosage }} · {{ $rx->frequency }} · {{ $rx->duration }}
                            </p>
                            @if($rx->instructions)
                            <p class="text-xs text-gray-400 mt-0.5">{{ $rx->instructions }}</p>
                            @endif
                        </div>
                        <span class="ml-auto px-2 py-0.5 rounded-full text-xs font-medium
                                     {{ $rx->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ ucfirst($rx->status) }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
    @empty
    <div class="bg-white dark:bg-gray-800 rounded-2xl py-20 text-center text-gray-400 shadow-sm">
        <i class="fa-solid fa-file-medical text-5xl mb-4"></i>
        <p class="text-lg font-medium">No medical records yet</p>
        <p class="text-sm mt-1">Your records will appear here after a consultation</p>
    </div>
    @endforelse

    @if($records->hasPages())
    <div>{{ $records->links() }}</div>
    @endif
</div>
@endsection
