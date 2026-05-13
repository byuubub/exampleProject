@extends('layouts.admin')

@section('title', 'System Settings')
@section('page-title', 'System Settings')

@section('content')

<div class="max-w-2xl mx-auto space-y-6 fade-in">

    @if(session('success'))
    <div class="p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center space-x-2">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">System Settings</h2>
        <p class="text-gray-500 dark:text-gray-400">Configure global system parameters</p>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
        @csrf

        {{-- Appointment Settings --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 rounded-lg bg-teal-100 flex items-center justify-center">
                    <i class="fa-solid fa-calendar-check text-teal-500"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Appointment Settings</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Configure appointment rules</p>
                </div>
            </div>
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        <i class="fa-solid fa-clock mr-1.5 text-teal-400"></i>
                        Priority Time Limit (minutes)
                    </label>
                    <input type="number" name="appointment_priority_time_limit"
                           value="{{ old('appointment_priority_time_limit', $settings->appointment_priority_time_limit ?? 30) }}"
                           min="1" max="120"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none">
                    <p class="text-xs text-gray-400 mt-1.5">
                        Minutes before appointment when patient loses priority if not yet checked in.
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                        <i class="fa-solid fa-calendar-days mr-1.5 text-teal-400"></i>
                        Max Advance Booking Days
                    </label>
                    <input type="number" name="max_appointment_days"
                           value="{{ old('max_appointment_days', $settings->max_appointment_days ?? 30) }}"
                           min="1" max="365"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none">
                    <p class="text-xs text-gray-400 mt-1.5">
                        How many days in advance patients can book appointments.
                    </p>
                </div>
            </div>
        </div>

        {{-- Queue Settings --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 rounded-lg bg-cyan-100 flex items-center justify-center">
                    <i class="fa-solid fa-list-ol text-cyan-500"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Queue Settings</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Configure queue management</p>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    <i class="fa-solid fa-rotate-right mr-1.5 text-cyan-400"></i>
                    Queue Reset Time
                </label>
                <input type="time" name="queue_reset_time"
                       value="{{ old('queue_reset_time', $settings->queue_reset_time ?? '00:00') }}"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none">
                <p class="text-xs text-gray-400 mt-1.5">
                    Time of day when the queue counter resets to 1 (e.g. midnight = 00:00).
                </p>
            </div>
        </div>

        {{-- Notification Settings --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                    <i class="fa-solid fa-bell text-purple-500"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Notifications</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Configure system notifications</p>
                </div>
            </div>
            <div class="space-y-4">
                @foreach([
                    ['name' => 'notify_appointment_created', 'label' => 'Notify on new appointment', 'desc' => 'Send email to doctor when a new appointment is booked'],
                    ['name' => 'notify_appointment_cancelled', 'label' => 'Notify on cancellation', 'desc' => 'Send email when an appointment is cancelled'],
                    ['name' => 'notify_payment_received', 'label' => 'Notify on payment', 'desc' => 'Send email receipt to patient after payment'],
                ] as $opt)
                <label class="flex items-start justify-between p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $opt['label'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $opt['desc'] }}</p>
                    </div>
                    <input type="checkbox" name="{{ $opt['name'] }}" value="1"
                           {{ old($opt['name'], $settings->{$opt['name']} ?? true) ? 'checked' : '' }}
                           class="w-5 h-5 rounded border-gray-300 text-teal-500 focus:ring-teal-500 ml-4 mt-0.5 flex-shrink-0">
                </label>
                @endforeach
            </div>
        </div>

        <button type="submit"
                class="w-full py-3.5 rounded-xl bg-gradient-to-r from-teal-500 to-teal-600 text-white font-semibold hover:from-teal-600 hover:to-teal-700 transition-all shadow-lg shadow-teal-500/25">
            <i class="fa-solid fa-floppy-disk mr-2"></i> Save Settings
        </button>
    </form>
</div>
@endsection
