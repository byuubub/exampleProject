@extends('layouts.public')

@section('title', 'Home')

@section('content')

{{-- Hero Section --}}
<section class="relative overflow-hidden bg-gradient-to-br from-teal-50 via-white to-cyan-50"
         x-data="heroCarousel()">
    <div class="absolute inset-0 opacity-30"
         style="background-image: radial-gradient(circle at 1px 1px, rgba(20, 184, 166, 0.15) 1px, transparent 0); background-size: 40px 40px;"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
        <div class="grid lg:grid-cols-2 gap-12 items-center">

            {{-- Left: Text --}}
            <div class="text-center lg:text-left fade-in">
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-teal-100 text-teal-700 text-sm font-medium mb-6">
                    <i class="fa-solid fa-shield-halved mr-2"></i>
                    Trusted Healthcare Platform
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight mb-6">
                    Your Health, Our
                    <span class="bg-gradient-to-r from-teal-500 to-cyan-500 bg-clip-text text-transparent"> Priority</span>
                </h1>
                <p class="text-lg text-gray-600 mb-8 max-w-xl">
                    Connect with top doctors, book appointments instantly, and manage your healthcare journey all in one place.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="{{ route('hospitals.index') }}"
                       class="inline-flex items-center justify-center px-8 py-4 rounded-2xl bg-gradient-to-r from-teal-500 to-teal-600 text-white font-semibold shadow-lg shadow-teal-500/30 hover:shadow-xl hover:shadow-teal-500/40 transition-all">
                        <i class="fa-solid fa-hospital mr-2"></i>
                        Find a Hospital
                    </a>
                    <a href="{{ route('about') }}"
                       class="inline-flex items-center justify-center px-8 py-4 rounded-2xl bg-white text-gray-700 font-semibold border-2 border-gray-200 hover:border-teal-300 hover:text-teal-600 transition-all">
                        Learn More
                        <i class="fa-solid fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>

            {{-- Right: Hospital Carousel --}}
            <div class="relative fade-in">
                <div class="relative rounded-3xl overflow-hidden shadow-2xl">
                    @foreach($featuredHospitals as $index => $hospital)
                    <div x-show="currentSlide === {{ $index }}"
                         x-transition:enter="transition-opacity duration-500"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         class="relative">
                        <img src="{{ $hospital['image'] }}" alt="{{ $hospital['name'] }}"
                             class="w-full h-80 lg:h-96 object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                            <h3 class="text-xl font-bold">{{ $hospital['name'] }}</h3>
                            <p class="text-white/80">{{ $hospital['city'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                {{-- Carousel Dots --}}
                <div class="flex justify-center mt-4 space-x-2">
                    @foreach($featuredHospitals as $index => $hospital)
                    <button @click="currentSlide = {{ $index }}"
                            class="h-3 rounded-full transition-all"
                            :class="currentSlide === {{ $index }} ? 'bg-teal-500 w-8' : 'bg-gray-300 hover:bg-gray-400 w-3'">
                    </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Stats Section --}}
<section class="py-12 bg-white border-y border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach([
                ['label' => 'Hospitals', 'value' => '50+', 'icon' => 'fa-hospital'],
                ['label' => 'Doctors', 'value' => '500+', 'icon' => 'fa-stethoscope'],
                ['label' => 'Patients Served', 'value' => '100K+', 'icon' => 'fa-users'],
                ['label' => 'Awards', 'value' => '25+', 'icon' => 'fa-award'],
            ] as $stat)
            <div class="text-center fade-in">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-teal-100 text-teal-600 mb-3">
                    <i class="fa-solid {{ $stat['icon'] }} text-lg"></i>
                </div>
                <div class="text-3xl font-bold text-gray-900">{{ $stat['value'] }}</div>
                <div class="text-gray-500">{{ $stat['label'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Specializations --}}
<section class="py-16 lg:py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">Browse by Specialization</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Find the right specialist for your health needs from our wide range of medical departments.
            </p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach($specializations as $spec)
            <a href="{{ route('hospitals.index') }}"
               class="group block p-6 bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all text-center card-hover">
                <div class="w-14 h-14 mx-auto rounded-xl bg-gradient-to-br {{ $spec['color'] }} flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <i class="fa-solid {{ $spec['icon'] }} text-white text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-1">{{ $spec['name'] }}</h3>
                <p class="text-sm text-gray-500">{{ $spec['doctors'] }} Doctors</p>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Features --}}
<section class="py-16 lg:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">Why Choose MedVerse?</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Experience healthcare made simple with our innovative platform.
            </p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            @foreach([
                ['icon' => 'fa-calendar-check', 'title' => 'Easy Appointment Booking',
                 'desc' => 'Book appointments with your preferred doctor in just a few clicks. No more waiting in long queues.', 'color' => 'teal'],
                ['icon' => 'fa-clock', 'title' => '24/7 Availability',
                 'desc' => 'Access healthcare services anytime, anywhere. Our platform is available round the clock.', 'color' => 'cyan'],
                ['icon' => 'fa-shield-halved', 'title' => 'Secure & Private',
                 'desc' => 'Your medical records are protected with enterprise-grade security and encryption.', 'color' => 'green'],
            ] as $feature)
            <div class="relative p-8 rounded-3xl bg-gradient-to-br from-gray-50 to-white border border-gray-100 hover:border-teal-200 transition-colors fade-in">
                <div class="w-14 h-14 rounded-2xl bg-{{ $feature['color'] }}-100 flex items-center justify-center mb-6">
                    <i class="fa-solid {{ $feature['icon'] }} text-{{ $feature['color'] }}-600 text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ $feature['title'] }}</h3>
                <p class="text-gray-600 leading-relaxed">{{ $feature['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA Section --}}
<section class="py-16 lg:py-24 bg-gradient-to-br from-teal-600 via-teal-700 to-cyan-700 relative overflow-hidden">
    <div class="absolute inset-0 opacity-20"
         style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.3) 1px, transparent 0); background-size: 40px 40px;"></div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
        <h2 class="text-3xl lg:text-4xl font-bold text-white mb-6">
            Ready to Take Control of Your Health?
        </h2>
        <p class="text-xl text-teal-100 mb-8 max-w-2xl mx-auto">
            Join thousands of patients who trust MedVerse for their healthcare needs.
        </p>
        <a href="{{ auth()->check() ? Auth::user()->getDashboardLink() : route('login') }}"
           class="inline-flex items-center px-8 py-4 rounded-2xl bg-white text-teal-600 font-semibold shadow-lg hover:shadow-xl transition-all">
            {{ auth()->check() ? 'Go to Dashboard' : 'Get Started Today' }}
            <i class="fa-solid fa-chevron-right ml-2"></i>
        </a>
    </div>
</section>

@endsection

@push('scripts')
<script>
function heroCarousel() {
    return {
        currentSlide: 0,
        total: {{ count($featuredHospitals) }},
        init() {
            setInterval(() => {
                this.currentSlide = (this.currentSlide + 1) % this.total;
            }, 5000);
        }
    }
}
</script>
@endpush
