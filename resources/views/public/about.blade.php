@extends('layouts.public')

@section('title', 'About Us')

@section('content')

{{-- Hero --}}
<section class="relative bg-gradient-to-br from-teal-600 via-teal-700 to-cyan-700 py-20 lg:py-32">
    <div class="absolute inset-0 bg-black/20"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center text-white fade-in">
        <h1 class="text-4xl lg:text-5xl font-bold mb-6">About MedVerse</h1>
        <p class="text-xl text-teal-100 max-w-3xl mx-auto">
            Revolutionizing healthcare management with innovative technology and compassionate care.
        </p>
    </div>
</section>

{{-- Mission & Vision --}}
<section class="py-16 lg:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12">
            <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-3xl p-8 lg:p-12 fade-in">
                <div class="w-16 h-16 rounded-2xl bg-teal-500 flex items-center justify-center mb-6">
                    <i class="fa-solid fa-bullseye text-white text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Our Mission</h2>
                <p class="text-gray-600 leading-relaxed mb-4">
                    To make quality healthcare accessible to everyone by bridging the gap between patients and healthcare providers through innovative technology solutions.
                </p>
                <ul class="space-y-3">
                    @foreach(['Connecting patients with the right specialists', 'Streamlining appointment scheduling', 'Digitizing medical records securely', 'Enabling seamless healthcare payments'] as $point)
                    <li class="flex items-start space-x-3">
                        <i class="fa-solid fa-circle-check text-teal-500 mt-1"></i>
                        <span class="text-gray-600">{{ $point }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>

            <div class="bg-gradient-to-br from-cyan-50 to-teal-50 rounded-3xl p-8 lg:p-12 fade-in">
                <div class="w-16 h-16 rounded-2xl bg-cyan-500 flex items-center justify-center mb-6">
                    <i class="fa-solid fa-eye text-white text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Our Vision</h2>
                <p class="text-gray-600 leading-relaxed mb-4">
                    To become Indonesia's leading digital healthcare ecosystem, empowering millions with instant access to world-class medical care.
                </p>
                <ul class="space-y-3">
                    @foreach(['A connected healthcare network nationwide', 'AI-powered health recommendations', 'Zero barriers to quality healthcare', 'A healthier, happier Indonesia'] as $point)
                    <li class="flex items-start space-x-3">
                        <i class="fa-solid fa-circle-check text-cyan-500 mt-1"></i>
                        <span class="text-gray-600">{{ $point }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- Values --}}
<section class="py-16 lg:py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">Our Core Values</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                The principles that guide everything we do at MedVerse.
            </p>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach([
                ['icon' => 'fa-heart', 'title' => 'Patient-Centered', 'desc' => 'Every decision we make puts patients first.', 'color' => 'rose'],
                ['icon' => 'fa-shield-halved', 'title' => 'Trust & Integrity', 'desc' => 'Building lasting relationships through honest care.', 'color' => 'teal'],
                ['icon' => 'fa-award', 'title' => 'Excellence', 'desc' => 'Striving for the highest standards in healthcare.', 'color' => 'amber'],
                ['icon' => 'fa-users', 'title' => 'Collaboration', 'desc' => 'Working together for better health outcomes.', 'color' => 'blue'],
            ] as $value)
            <div class="bg-white rounded-2xl p-6 text-center shadow-sm hover:shadow-md transition-shadow card-hover">
                <div class="w-14 h-14 rounded-xl bg-{{ $value['color'] }}-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid {{ $value['icon'] }} text-{{ $value['color'] }}-500 text-xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">{{ $value['title'] }}</h3>
                <p class="text-gray-500 text-sm">{{ $value['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Team --}}
<section class="py-16 lg:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">Meet Our Leadership</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                The dedicated team driving innovation in healthcare technology.
            </p>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($team as $member)
            <div class="text-center card-hover">
                <div class="relative mb-4">
                    <img src="{{ $member['image'] }}" alt="{{ $member['name'] }}"
                         class="w-32 h-32 rounded-2xl mx-auto object-cover shadow-lg">
                    <div class="absolute inset-0 rounded-2xl bg-gradient-to-t from-teal-500/20 to-transparent mx-auto w-32 h-32"></div>
                </div>
                <h3 class="font-semibold text-gray-900">{{ $member['name'] }}</h3>
                <p class="text-teal-600 text-sm">{{ $member['role'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Stats --}}
<section class="py-16 bg-gradient-to-br from-teal-600 to-cyan-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center text-white">
            @foreach([
                ['label' => 'Hospitals', 'value' => '50+'],
                ['label' => 'Doctors', 'value' => '500+'],
                ['label' => 'Patients Served', 'value' => '100K+'],
                ['label' => 'Years of Excellence', 'value' => '10+'],
            ] as $stat)
            <div>
                <div class="text-4xl font-bold mb-2">{{ $stat['value'] }}</div>
                <div class="text-teal-100">{{ $stat['label'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

@endsection
