@extends('layouts.admin')

@section('title', 'Appointment Management')
@section('page-title', 'Appointment Management')

@section('content')

<div class="space-y-6 fade-in" x-data="{ search: '', statusFilter: '' }">

    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Appointments</h2>
        <p class="text-gray-500 dark:text-gray-400">Manage today's appointments for {{ Auth::user()->hospital->name ?? 'the hospital' }}</p>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" x-model="search" placeholder="Search patient or doctor..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none">
            </div>
            <select x-model="statusFilter"
                    class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none">
                <option value="">All Status</option>
                <option value="scheduled">Scheduled</option>
                <option value="confirmed">Confirmed</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700/50 text-left">
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Patient</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Doctor</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Time</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($appointments as $appt)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors"
                    x-show="
                        (!search || '{{ strtolower(($appt->patient->user->full_name ?? '').' '.($appt->doctor->user->full_name ?? '')) }}'.includes(search.toLowerCase())) &&
                        (!statusFilter || '{{ $appt->status }}' === statusFilter)
                    ">
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-full bg-teal-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-teal-600 font-semibold text-sm">
                                    {{ strtoupper(substr($appt->patient->user->full_name ?? 'P', 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $appt->patient->user->full_name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">{{ $appt->complaint ? Str::limit($appt->complaint, 30) : 'No complaint noted' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 hidden md:table-cell">
                        Dr. {{ $appt->doctor->user->full_name ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 text-sm hidden md:table-cell">
                        <p class="text-gray-700 dark:text-gray-300">{{ \Carbon\Carbon::parse($appt->appointment_date)->format('d M Y') }}</p>
                        <p class="text-gray-500 dark:text-gray-400">{{ $appt->appointment_time }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium
                            {{ match($appt->status) {
                                'scheduled'  => 'bg-blue-100 text-blue-700',
                                'confirmed'  => 'bg-teal-100 text-teal-700',
                                'completed'  => 'bg-green-100 text-green-700',
                                'cancelled'  => 'bg-red-100 text-red-700',
                                default      => 'bg-gray-100 text-gray-600',
                            } }}">
                            {{ ucfirst($appt->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            @if($appt->status === 'scheduled')
                            <form method="POST" action="{{ route('staff.appointments.update', $appt->id) }}" class="inline">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="confirmed">
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-teal-500 text-white text-xs font-medium hover:bg-teal-600">
                                    Confirm
                                </button>
                            </form>
                            @endif
                            @if(in_array($appt->status, ['scheduled', 'confirmed']))
                            <form method="POST" action="{{ route('staff.appointments.update', $appt->id) }}" class="inline">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit"
                                        onclick="return confirm('Cancel this appointment?')"
                                        class="px-3 py-1.5 rounded-lg border border-red-200 text-red-500 text-xs font-medium hover:bg-red-50">
                                    Cancel
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center text-gray-400">
                        <i class="fa-solid fa-calendar-xmark text-4xl mb-3"></i>
                        <p>No appointments found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($appointments->hasPages())
        <div class="px-6 py-4 border-t dark:border-gray-700">{{ $appointments->links() }}</div>
        @endif
    </div>
</div>
@endsection
