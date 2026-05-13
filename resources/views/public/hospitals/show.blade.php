@extends('layouts.public')

@section('title', $hospital->name)

@section('content')

{{-- Hero --}}
<section class="relative h-64 lg:h-80">
    @if($hospital->image_url)
    <img src="{{ $hospital->image_url }}" alt="{{ $hospital->name }}" class="w-full h-full object-cover">
    @else
    <div class="w-full h-full bg-gradient-to-br from-teal-400 to-cyan-500"></div>
    @endif
    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
    <div class="absolute bottom-0 left-0 right-0 p-6 lg:p-12 text-white">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center space-x-2 text-teal-300 text-sm mb-2">
                <a href="{{ route('hospitals.index') }}" class="hover:text-white">Hospitals</a>
                <i class="fa-solid fa-chevron-right text-xs"></i>
                <span>{{ $hospital->name }}</span>
            </div>
            <h1 class="text-3xl lg:text-4xl font-bold">{{ $hospital->name }}</h1>
            <p class="text-white/80 mt-2">
                <i class="fa-solid fa-location-dot mr-2"></i>{{ $hospital->city }}
            </p>
        </div>
    </div>
</section>

{{-- Content --}}
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-3 gap-8">

            {{-- Main Info --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- About --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">About This Hospital</h2>
                    <p class="text-gray-600 leading-relaxed">
                        {{ $hospital->description ?? 'A premier healthcare facility committed to providing excellent medical services to our community.' }}
                    </p>
                </div>

                {{-- Doctors --}}
                @if($doctors && $doctors->count() > 0)
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Our Doctors</h2>
                    <div class="grid md:grid-cols-2 gap-4">
                        @foreach($doctors as $doctor)
                        <a href="{{ route('doctors.show', $doctor->id) }}"
                           class="flex items-center space-x-4 p-4 rounded-xl border border-gray-100 hover:border-teal-200 hover:bg-teal-50 transition-all">
                            <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-teal-100 to-cyan-100 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-user-doctor text-teal-500 text-xl"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-900">{{ $doctor->full_name }}</p>
                                <p class="text-sm text-teal-600">{{ $doctor->specialization->name ?? 'General' }}</p>
                                @if($doctor->consultation_fee)
                                <p class="text-sm text-gray-500">Rp {{ number_format($doctor->consultation_fee, 0, ',', '.') }}</p>
                                @endif
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Contact Card --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h2>
                    <ul class="space-y-4">
                        @if($hospital->address)
                        <li class="flex items-start space-x-3">
                            <div class="w-9 h-9 rounded-lg bg-teal-100 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-location-dot text-teal-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5">Address</p>
                                <p class="text-gray-700 text-sm">{{ $hospital->address }}</p>
                            </div>
                        </li>
                        @endif
                        @if($hospital->phone)
                        <li class="flex items-start space-x-3">
                            <div class="w-9 h-9 rounded-lg bg-teal-100 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-phone text-teal-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5">Phone</p>
                                <p class="text-gray-700 text-sm">{{ $hospital->phone }}</p>
                            </div>
                        </li>
                        @endif
                        @if($hospital->email)
                        <li class="flex items-start space-x-3">
                            <div class="w-9 h-9 rounded-lg bg-teal-100 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-envelope text-teal-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5">Email</p>
                                <p class="text-gray-700 text-sm">{{ $hospital->email }}</p>
                            </div>
                        </li>
                        @endif
                    </ul>
                </div>

                {{-- Book Appointment CTA --}}
                <div class="bg-gradient-to-br from-teal-500 to-cyan-600 rounded-2xl p-6 text-white text-center">
                    <i class="fa-solid fa-calendar-check text-3xl mb-3"></i>
                    <h3 class="font-semibold text-lg mb-2">Book an Appointment</h3>
                    <p class="text-teal-100 text-sm mb-4">Schedule a visit with our doctors today</p>
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
