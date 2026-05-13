@extends('layouts.admin')
@section('title', 'Prescriptions')
@section('page-title', 'Prescriptions')

@section('content')
<div class="space-y-6 fade-in">

    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Prescriptions</h2>
        <p class="text-gray-500 dark:text-gray-400">
            @if(Auth::user()->role === 'doctor') Prescriptions you have issued @else Your active prescriptions @endif
        </p>
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-3 gap-4">
        @php
            $active    = $prescriptions->getCollection()->where('status', 'active')->count();
            $completed = $prescriptions->getCollection()->where('status', 'completed')->count();
            $cancelled = $prescriptions->getCollection()->where('status', 'cancelled')->count();
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm text-center">
            <p class="text-2xl font-bold text-green-500">{{ $active }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Active</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm text-center">
            <p class="text-2xl font-bold text-gray-400">{{ $completed }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Completed</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm text-center">
            <p class="text-2xl font-bold text-red-400">{{ $cancelled }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Cancelled</p>
        </div>
    </div>

    {{-- Prescriptions Grid --}}
    <div class="grid md:grid-cols-2 gap-4">
        @forelse($prescriptions as $rx)
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center">
                        <i class="fa-solid fa-pills text-teal-500"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $rx->medicine_name }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ $rx->created_at->format('d M Y') }}</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-medium
                             {{ $rx->status === 'active' ? 'bg-green-100 text-green-700' :
                                ($rx->status === 'completed' ? 'bg-gray-100 text-gray-600' : 'bg-red-100 text-red-600') }}">
                    {{ ucfirst($rx->status) }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                @if($rx->dosage)
                <div class="flex items-center space-x-2 text-gray-600 dark:text-gray-400">
                    <i class="fa-solid fa-weight-scale text-teal-400 w-4"></i>
                    <span>{{ $rx->dosage }}</span>
                </div>
                @endif
                @if($rx->frequency)
                <div class="flex items-center space-x-2 text-gray-600 dark:text-gray-400">
                    <i class="fa-solid fa-clock text-teal-400 w-4"></i>
                    <span>{{ $rx->frequency }}</span>
                </div>
                @endif
                @if($rx->duration)
                <div class="flex items-center space-x-2 text-gray-600 dark:text-gray-400">
                    <i class="fa-solid fa-calendar text-teal-400 w-4"></i>
                    <span>{{ $rx->duration }}</span>
                </div>
                @endif
            </div>

            @if($rx->instructions)
            <div class="p-3 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 text-sm">
                <i class="fa-solid fa-circle-info mr-1.5"></i>{{ $rx->instructions }}
            </div>
            @endif

            <div class="mt-3 pt-3 border-t dark:border-gray-700 text-xs text-gray-400">
                @if(Auth::user()->role === 'doctor')
                Patient: {{ $rx->patient->user->full_name ?? 'N/A' }}
                @else
                Prescribed by: Dr. {{ $rx->doctor->user->full_name ?? 'N/A' }}
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-2 bg-white dark:bg-gray-800 rounded-2xl py-20 text-center text-gray-400 shadow-sm">
            <i class="fa-solid fa-prescription-bottle-medical text-5xl mb-4"></i>
            <p class="text-lg font-medium">No prescriptions found</p>
        </div>
        @endforelse
    </div>

    @if($prescriptions->hasPages())
    <div>{{ $prescriptions->links() }}</div>
    @endif
</div>
@endsection
