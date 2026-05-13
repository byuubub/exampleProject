@extends('layouts.public')

@section('title', 'Our Doctors')

@section('content')

<section class="bg-gradient-to-br from-teal-600 via-teal-700 to-cyan-700 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white fade-in">
        <h1 class="text-4xl lg:text-5xl font-bold mb-4">Our Doctors</h1>
        <p class="text-xl text-teal-100 max-w-2xl mx-auto mb-8">Expert specialists dedicated to your health and wellbeing</p>
        <form method="GET" action="{{ route('doctors.index') }}" class="max-w-2xl mx-auto relative">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by name or specialization..."
                   class="w-full pl-12 pr-4 py-4 rounded-2xl bg-white text-gray-900 placeholder-gray-500 outline-none shadow-lg focus:ring-4 focus:ring-white/20">
        </form>
    </div>
</section>

{{-- Specialization Filter --}}
<section class="py-4 bg-white border-b sticky top-16 z-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center space-x-2 overflow-x-auto pb-2">
            <a href="{{ route('doctors.index', array_merge(request()->query(), ['spec' => ''])) }}"
               class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-colors
                      {{ !request('spec') ? 'bg-teal-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                All Specializations
            </a>
            @foreach($specializations as $spec)
            <a href="{{ route('doctors.index', array_merge(request()->query(), ['spec' => $spec->id])) }}"
               class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-colors
                      {{ request('spec') == $spec->id ? 'bg-teal-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                {{ $spec->name }}
            </a>
            @endforeach
        </div>
    </div>
</section>

<section class="py-10 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($doctors->count() === 0)
        <div class="text-center py-16 text-gray-400">
            <i class="fa-solid fa-user-doctor text-4xl mb-3"></i>
            <p class="font-medium">No doctors found</p>
        </div>
        @else
        <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($doctors as $doctor)
            <a href="{{ route('doctors.show', $doctor->id) }}"
               class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-xl transition-all card-hover text-center block">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-teal-100 to-cyan-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-user-doctor text-teal-500 text-3xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-1">{{ $doctor->user->full_name ?? 'N/A' }}</h3>
                <p class="text-teal-600 text-sm mb-2">{{ $doctor->specialization->name ?? 'General' }}</p>
                @if($doctor->hospital)
                <p class="text-gray-500 text-xs mb-3">
                    <i class="fa-solid fa-hospital mr-1"></i> {{ $doctor->hospital->name }}
                </p>
                @endif
                @if($doctor->experience_years)
                <p class="text-gray-400 text-xs mb-3">
                    <i class="fa-solid fa-graduation-cap mr-1"></i> {{ $doctor->experience_years }} yrs exp
                </p>
                @endif
                @if($doctor->consultation_fee)
                <div class="pt-3 border-t border-gray-100">
                    <p class="text-sm font-semibold text-teal-600">Rp {{ number_format($doctor->consultation_fee, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400">per consultation</p>
                </div>
                @endif
            </a>
            @endforeach
        </div>
        @if($doctors->hasPages())
        <div class="mt-8">{{ $doctors->withQueryString()->links() }}</div>
        @endif
        @endif
    </div>
</section>

@endsection
