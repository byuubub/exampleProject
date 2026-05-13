@extends('layouts.public')

@section('title', 'Our Hospitals')

@section('content')

{{-- Header --}}
<section class="bg-gradient-to-br from-teal-600 via-teal-700 to-cyan-700 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white fade-in">
        <h1 class="text-4xl lg:text-5xl font-bold mb-4">Our Hospitals</h1>
        <p class="text-xl text-teal-100 max-w-2xl mx-auto mb-8">
            Find the best healthcare facility near you from our network of hospitals
        </p>
        <form method="GET" action="{{ route('hospitals.index') }}" class="max-w-2xl mx-auto relative">
            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by hospital name or city..."
                   class="w-full pl-12 pr-4 py-4 rounded-2xl bg-white text-gray-900 placeholder-gray-500 focus:ring-4 focus:ring-white/20 outline-none shadow-lg">
        </form>
    </div>
</section>

{{-- City Filter --}}
<section class="py-4 bg-white border-b sticky top-16 z-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center space-x-2 overflow-x-auto pb-2">
            <a href="{{ route('hospitals.index', array_merge(request()->query(), ['city' => ''])) }}"
               class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-colors
                      {{ !request('city') ? 'bg-teal-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                All Cities
            </a>
            @foreach($cities as $city)
            <a href="{{ route('hospitals.index', array_merge(request()->query(), ['city' => $city])) }}"
               class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-colors
                      {{ request('city') === $city ? 'bg-teal-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                {{ $city }}
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Hospitals Grid --}}
<section class="py-8 lg:py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($hospitals->count() === 0)
        <div class="text-center py-16">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-hospital text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No hospitals found</h3>
            <p class="text-gray-500">Try adjusting your search or filters</p>
        </div>
        @else
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($hospitals as $hospital)
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all card-hover">
                @if($hospital->image_url)
                <img src="{{ $hospital->image_url }}" alt="{{ $hospital->name }}"
                     class="w-full h-48 object-cover">
                @else
                <div class="w-full h-48 bg-gradient-to-br from-teal-100 to-cyan-100 flex items-center justify-center">
                    <i class="fa-solid fa-hospital text-teal-300 text-5xl"></i>
                </div>
                @endif
                <div class="p-6">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $hospital->name }}</h3>
                            <div class="flex items-center text-gray-500 text-sm">
                                <i class="fa-solid fa-location-dot mr-1.5 text-teal-500"></i>
                                {{ $hospital->city }}
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                     {{ $hospital->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($hospital->status) }}
                        </span>
                    </div>

                    @if($hospital->address)
                    <p class="text-sm text-gray-500 mb-3 flex items-start">
                        <i class="fa-solid fa-map-marker-alt mr-2 mt-0.5 text-gray-400"></i>
                        {{ Str::limit($hospital->address, 80) }}
                    </p>
                    @endif

                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        @if($hospital->phone)
                        <span class="text-sm text-gray-500 flex items-center">
                            <i class="fa-solid fa-phone mr-1.5 text-teal-500"></i>
                            {{ $hospital->phone }}
                        </span>
                        @endif
                        <a href="{{ route('hospitals.show', $hospital->id) }}"
                           class="flex items-center text-teal-600 hover:text-teal-700 text-sm font-medium">
                            View Details
                            <i class="fa-solid fa-chevron-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($hospitals->hasPages())
        <div class="mt-8">
            {{ $hospitals->withQueryString()->links() }}
        </div>
        @endif
        @endif
    </div>
</section>

@endsection
