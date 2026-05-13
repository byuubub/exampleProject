<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MedVerse') - Trusted Healthcare Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        .fade-in { animation: fadeIn 0.5s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        .card-hover { transition: transform 0.2s, box-shadow 0.2s; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
    </style>
    @stack('styles')
</head>
<body class="antialiased bg-white">

{{-- Navbar --}}
<nav class="bg-white/95 backdrop-blur-md shadow-sm sticky top-0 z-50" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            {{-- Logo --}}
            <div class="flex items-center">
                <a href="{{ url('/') }}" class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-stethoscope text-white text-lg"></i>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-teal-600 to-cyan-600 bg-clip-text text-transparent">
                        MedVerse
                    </span>
                </a>
            </div>

            {{-- Desktop Menu --}}
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ url('/') }}" class="text-gray-600 hover:text-teal-600 transition-colors font-medium {{ Request::is('/') ? 'text-teal-600' : '' }}">Home</a>
                <a href="{{ route('hospitals.index') }}" class="text-gray-600 hover:text-teal-600 transition-colors font-medium {{ Request::is('hospitals*') ? 'text-teal-600' : '' }}">Hospitals</a>
                <a href="{{ route('doctors.index') }}" class="text-gray-600 hover:text-teal-600 transition-colors font-medium {{ Request::is('doctors*') ? 'text-teal-600' : '' }}">Doctors</a>
                <a href="{{ route('about') }}" class="text-gray-600 hover:text-teal-600 transition-colors font-medium {{ Request::is('about') ? 'text-teal-600' : '' }}">About</a>
                <a href="{{ route('contact') }}" class="text-gray-600 hover:text-teal-600 transition-colors font-medium {{ Request::is('contact') ? 'text-teal-600' : '' }}">Contact</a>
            </div>

            {{-- Auth Buttons --}}
            <div class="hidden md:flex items-center space-x-4">
                @auth
                    <a href="{{ Auth::user()->getDashboardLink() }}"
                       class="flex items-center space-x-2 px-4 py-2 rounded-lg bg-teal-50 text-teal-600 hover:bg-teal-100 transition-colors">
                        <i class="fa-solid fa-user w-4"></i>
                        <span class="font-medium">{{ Auth::user()->full_name }}</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                                class="flex items-center space-x-2 px-4 py-2 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
                            <i class="fa-solid fa-right-from-bracket w-4"></i>
                            <span>Sign Out</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-teal-500 to-teal-600 text-white font-medium hover:from-teal-600 hover:to-teal-700 transition-all shadow-lg shadow-teal-500/25">
                        Sign In
                    </a>
                @endauth
            </div>

            {{-- Mobile Menu Button --}}
            <div class="md:hidden flex items-center">
                <button @click="open = !open" class="p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                    <i x-show="!open" class="fa-solid fa-bars w-6 h-6"></i>
                    <i x-show="open" x-cloak class="fa-solid fa-xmark w-6 h-6"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="open" x-cloak class="md:hidden bg-white border-t">
        <div class="px-4 py-4 space-y-3">
            <a href="{{ url('/') }}" @click="open = false"
               class="block px-4 py-2 rounded-lg text-gray-600 hover:bg-teal-50 hover:text-teal-600">Home</a>
            <a href="{{ route('hospitals.index') }}" @click="open = false"
               class="block px-4 py-2 rounded-lg text-gray-600 hover:bg-teal-50 hover:text-teal-600">Hospitals</a>
            <a href="{{ route('doctors.index') }}" @click="open = false"
               class="block px-4 py-2 rounded-lg text-gray-600 hover:bg-teal-50 hover:text-teal-600">Doctors</a>
            <a href="{{ route('about') }}" @click="open = false"
               class="block px-4 py-2 rounded-lg text-gray-600 hover:bg-teal-50 hover:text-teal-600">About</a>
            <a href="{{ route('contact') }}" @click="open = false"
               class="block px-4 py-2 rounded-lg text-gray-600 hover:bg-teal-50 hover:text-teal-600">Contact</a>
            <div class="pt-3 border-t">
                @auth
                    <a href="{{ Auth::user()->getDashboardLink() }}" @click="open = false"
                       class="block px-4 py-2 rounded-lg bg-teal-50 text-teal-600 font-medium">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit"
                                class="w-full px-4 py-2 rounded-lg bg-gray-100 text-gray-600 font-medium text-left">
                            Sign Out
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" @click="open = false"
                       class="block px-4 py-2.5 rounded-xl bg-gradient-to-r from-teal-500 to-teal-600 text-white text-center font-medium">
                        Sign In
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- Page Content --}}
@yield('content')

{{-- Footer --}}
<footer class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {{-- Brand --}}
            <div class="space-y-4">
                <a href="{{ url('/') }}" class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-teal-400 to-teal-500 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-stethoscope text-white text-lg"></i>
                    </div>
                    <span class="text-xl font-bold">MedVerse</span>
                </a>
                <p class="text-gray-400 text-sm leading-relaxed">
                    Your trusted healthcare partner. Connecting patients with the best doctors and hospitals for a healthier tomorrow.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="w-10 h-10 rounded-lg bg-gray-700 hover:bg-teal-500 flex items-center justify-center transition-colors">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-lg bg-gray-700 hover:bg-teal-500 flex items-center justify-center transition-colors">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-lg bg-gray-700 hover:bg-teal-500 flex items-center justify-center transition-colors">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-lg bg-gray-700 hover:bg-teal-500 flex items-center justify-center transition-colors">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>

            {{-- Quick Links --}}
            <div>
                <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('hospitals.index') }}" class="text-gray-400 hover:text-teal-400 transition-colors">Our Hospitals</a></li>
                    <li><a href="{{ route('about') }}" class="text-gray-400 hover:text-teal-400 transition-colors">About Us</a></li>
                    <li><a href="{{ route('contact') }}" class="text-gray-400 hover:text-teal-400 transition-colors">Contact</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-teal-400 transition-colors">Privacy Policy</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-teal-400 transition-colors">Terms of Service</a></li>
                </ul>
            </div>

            {{-- Services --}}
            <div>
                <h3 class="text-lg font-semibold mb-4">Our Services</h3>
                <ul class="space-y-2">
                    <li class="text-gray-400">Online Appointment</li>
                    <li class="text-gray-400">Medical Records</li>
                    <li class="text-gray-400">Health Consultation</li>
                    <li class="text-gray-400">Emergency Care</li>
                    <li class="text-gray-400">Lab Tests</li>
                </ul>
            </div>

            {{-- Contact --}}
            <div>
                <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
                <ul class="space-y-3">
                    <li class="flex items-start space-x-3">
                        <i class="fa-solid fa-location-dot text-teal-400 mt-1 flex-shrink-0"></i>
                        <span class="text-gray-400">123 Healthcare Avenue, Medical District, Jakarta 12345</span>
                    </li>
                    <li class="flex items-center space-x-3">
                        <i class="fa-solid fa-phone text-teal-400 flex-shrink-0"></i>
                        <span class="text-gray-400">+62 21 1234 5678</span>
                    </li>
                    <li class="flex items-center space-x-3">
                        <i class="fa-solid fa-envelope text-teal-400 flex-shrink-0"></i>
                        <span class="text-gray-400">info@medverse.id</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-700 mt-10 pt-8 text-center text-gray-400 text-sm">
            <p>&copy; {{ date('Y') }} MedVerse. All rights reserved. Made with ❤️ for better healthcare.</p>
        </div>
    </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.5/cdn.min.js" defer></script>
@stack('scripts')
</body>
</html>
