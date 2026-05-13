@extends('layouts.public')

@section('title', 'Contact Us')

@section('content')

{{-- Hero --}}
<section class="bg-gradient-to-br from-teal-600 to-cyan-700 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white fade-in">
        <h1 class="text-4xl lg:text-5xl font-bold mb-4">Contact Us</h1>
        <p class="text-xl text-teal-100 max-w-2xl mx-auto">
            Have questions? We're here to help you with anything you need.
        </p>
    </div>
</section>

{{-- Content --}}
<section class="py-16 lg:py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-3 gap-8">

            {{-- Contact Info --}}
            <div class="space-y-6">
                @foreach([
                    ['icon' => 'fa-location-dot', 'title' => 'Address', 'value' => '123 Healthcare Avenue, Medical District, Jakarta 12345', 'color' => 'teal'],
                    ['icon' => 'fa-phone', 'title' => 'Phone', 'value' => '+62 21 1234 5678', 'color' => 'blue'],
                    ['icon' => 'fa-envelope', 'title' => 'Email', 'value' => 'info@medverse.id', 'color' => 'purple'],
                    ['icon' => 'fa-clock', 'title' => 'Working Hours', 'value' => 'Monday - Friday: 8AM - 6PM', 'color' => 'amber'],
                ] as $info)
                <div class="bg-white rounded-2xl p-5 shadow-sm flex items-start space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-{{ $info['color'] }}-100 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid {{ $info['icon'] }} text-{{ $info['color'] }}-500 text-lg"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 mb-1">{{ $info['title'] }}</p>
                        <p class="text-gray-600 text-sm">{{ $info['value'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Contact Form --}}
            <div class="lg:col-span-2 bg-white rounded-2xl p-6 lg:p-8 shadow-sm">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Send Us a Message</h2>

                @if(session('status'))
                <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700">
                    <i class="fa-solid fa-circle-check mr-2"></i>
                    {{ session('status') }}
                </div>
                @endif

                <form method="POST" action="{{ route('contact.send') }}" class="space-y-5">
                    @csrf

                    <div class="grid md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none @error('name') border-red-400 @enderror"
                                   placeholder="John Doe">
                            @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address *</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none @error('email') border-red-400 @enderror"
                                   placeholder="you@example.com">
                            @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Subject *</label>
                        <input type="text" name="subject" value="{{ old('subject') }}" required
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none @error('subject') border-red-400 @enderror"
                               placeholder="How can we help?">
                        @error('subject')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Message *</label>
                        <textarea name="message" rows="6" required
                                  class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none resize-none @error('message') border-red-400 @enderror"
                                  placeholder="Tell us more about your inquiry...">{{ old('message') }}</textarea>
                        @error('message')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            class="w-full py-3.5 rounded-xl bg-gradient-to-r from-teal-500 to-teal-600 text-white font-semibold hover:from-teal-600 hover:to-teal-700 transition-all shadow-lg shadow-teal-500/25">
                        <i class="fa-solid fa-paper-plane mr-2"></i>
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection
