@extends('layouts.admin')

@section('title', 'Patient List')
@section('page-title', 'Patient List')

@section('content')

<div class="space-y-6 fade-in" x-data="{ search: '' }">

    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Patients</h2>
        <p class="text-gray-500 dark:text-gray-400">View all registered patients</p>
    </div>

    {{-- Search --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm">
        <div class="relative max-w-md">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" x-model="search" placeholder="Search by name or MRN..."
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none">
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700/50 text-left">
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Patient</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">MRN</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Date of Birth</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Blood Type</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Insurance</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($patients as $patient)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors"
                    x-show="!search || '{{ strtolower($patient->user->full_name ?? '') }} {{ strtolower($patient->medical_record_number ?? '') }}'.includes(search.toLowerCase())">
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-full bg-teal-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-teal-600 font-semibold text-sm">
                                    {{ strtoupper(substr($patient->user->full_name ?? 'P', 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $patient->user->full_name ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-500">{{ $patient->user->email ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm font-mono text-gray-600 dark:text-gray-400 hidden md:table-cell">
                        {{ $patient->medical_record_number ?? '—' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 hidden md:table-cell">
                        {{ $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->format('d M Y') : '—' }}
                    </td>
                    <td class="px-6 py-4 hidden lg:table-cell">
                        @if($patient->blood_type)
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-600">
                            {{ $patient->blood_type }}
                        </span>
                        @else
                        <span class="text-gray-400 text-sm">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 hidden lg:table-cell">
                        {{ $patient->insurance_provider ?? '—' }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('dashboard.patient.history', $patient->id) }}"
                           class="inline-flex items-center px-3 py-1.5 rounded-lg text-teal-600 border border-teal-200 hover:bg-teal-50 text-xs font-medium transition-colors">
                            <i class="fa-solid fa-eye mr-1"></i> View Records
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center text-gray-400">
                        <i class="fa-solid fa-hospital-user text-4xl mb-3"></i>
                        <p>No patients registered yet</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($patients->hasPages())
        <div class="px-6 py-4 border-t dark:border-gray-700">{{ $patients->links() }}</div>
        @endif
    </div>
</div>
@endsection
