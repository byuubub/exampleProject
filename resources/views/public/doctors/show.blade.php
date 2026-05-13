@extends('layouts.public')

@section('title', 'Dr. ' . ($doctor->user->full_name ?? 'Doctor'))

@section('content')

{{-- Hero --}}
<section class="bg-gradient-to-br from-teal-600 via-teal-700 to-cyan-700 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center space-x-2 text-teal-200 text-sm mb-6">
            <a href="{{ route('doctors.index') }}" class="hover:text-white">Doctors</a>
            <i class="fa-solid fa-chevron-right text-xs"></i>
            <span>{{ $doctor->user->full_name ?? 'Doctor' }}</span>
        </div>
        <div class="flex flex-col md:flex-row items-center md:items-start gap-8 text-white fade-in">
            <div class="w-32 h-32 rounded-3xl bg-white/20 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-user-doctor text-6xl text-white/80"></i>
            </div>
            <div>
                <h1 class="text-3xl lg:text-4xl font-bold mb-2">Dr. {{ $doctor->user->full_name ?? 'N/A' }}</h1>
                <p class="text-teal-200 text-lg mb-3">{{ $doctor->specialization->name ?? 'General Medicine' }}</p>
                <div class="flex flex-wrap gap-4 text-teal-100 text-sm">
                    @if($doctor->hospital)
                    <span><i class="fa-solid fa-hospital mr-1.5"></i>{{ $doctor->hospital->name }}</span>
                    @endif
                    @if($doctor->experience_years)
                    <span><i class="fa-solid fa-graduation-cap mr-1.5"></i>{{ $doctor->experience_years }} Years Experience</span>
                    @endif
                    @if($doctor->license_number)
                    <span><i class="fa-solid fa-id-card mr-1.5"></i>{{ $doctor->license_number }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Content --}}
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-3 gap-8">

            {{-- Main --}}
            <div class="lg:col-span-2 space-y-6">
                @if($doctor->bio)
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">About</h2>
                    <p class="text-gray-600 leading-relaxed">{{ $doctor->bio }}</p>
                </div>
                @endif

                @if($doctor->education)
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Education</h2>
                    <p class="text-gray-600 leading-relaxed">{{ $doctor->education }}</p>
                </div>
                @endif

                {{-- Schedules --}}
                @if($schedules && $schedules->count() > 0)
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Practice Schedule</h2>
                    <div class="space-y-3">
                        @foreach($schedules as $schedule)
                        @if($schedule->is_active)
                        <div class="flex items-center justify-between p-4 rounded-xl bg-teal-50 border border-teal-100">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg bg-teal-500 flex items-center justify-center">
                                    <i class="fa-solid fa-calendar-day text-white text-sm"></i>
                                </div>
                                <span class="font-semibold text-gray-900">{{ $schedule->day_of_week }}</span>
                            </div>
                            <div class="text-right">
                                <p class="text-teal-700 font-medium">{{ $schedule->start_time }} – {{ $schedule->end_time }}</p>
                                @if($schedule->quota)
                                <p class="text-sm text-gray-500">{{ $schedule->quota }} patients/day</p>
                                @endif
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Fee Card --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h3 class="font-semibold text-gray-900 mb-4">Consultation Details</h3>
                    @if($doctor->consultation_fee)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <span class="text-gray-500">Consultation Fee</span>
                        <span class="font-bold text-teal-600 text-lg">Rp {{ number_format($doctor->consultation_fee, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($doctor->specialization)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <span class="text-gray-500">Specialization</span>
                        <span class="font-medium text-gray-900">{{ $doctor->specialization->name }}</span>
                    </div>
                    @endif
                    @if($doctor->hospital)
                    <div class="flex items-center justify-between py-3">
                        <span class="text-gray-500">Hospital</span>
                        <span class="font-medium text-gray-900 text-right max-w-[60%]">{{ $doctor->hospital->name }}</span>
                    </div>
                    @endif
                </div>

                {{-- Book CTA --}}
                <div class="bg-gradient-to-br from-teal-500 to-cyan-600 rounded-2xl p-6 text-white text-center">
                    <i class="fa-solid fa-calendar-plus text-3xl mb-3"></i>
                    <h3 class="font-semibold text-lg mb-2">Book an Appointment</h3>
                    <p class="text-teal-100 text-sm mb-4">Schedule a consultation with this doctor</p>
                    <a href="{{ auth()->check() ? route('dashboard.appointments') : route('login') }}"
                       class="block w-full py-3 rounded-xl bg-white text-teal-600 font-semibold hover:bg-teal-50 transition-colors">
                        {{ auth()->check() ? 'Book Now' : 'Sign In to Book' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
