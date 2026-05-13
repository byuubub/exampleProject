<!DOCTYPE html>
<html lang="en" x-data="adminLayout()" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - MedVerse Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
        .fade-in { animation: fadeIn 0.4s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        .sidebar-transition { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>
    @stack('styles')
</head>
<body class="antialiased" :class="darkMode ? 'bg-gray-900' : 'bg-gray-50'">

<div class="min-h-screen flex">

    {{-- Mobile Overlay --}}
    <div x-show="mobileMenuOpen"
         x-cloak
         @click="mobileMenuOpen = false"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>

    {{-- Sidebar --}}
    <aside class="fixed top-0 left-0 h-full w-64 z-50 sidebar-transition shadow-xl"
           :class="[
               sidebarOpen || mobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
               darkMode ? 'bg-gray-800' : 'bg-white'
           ]">

        {{-- Logo --}}
        <div class="h-16 flex items-center justify-between px-4 border-b"
             :class="darkMode ? 'border-gray-700' : 'border-gray-100'">
            <a href="{{ url('/') }}" class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-gradient-to-br from-teal-500 to-teal-600 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-stethoscope text-white text-sm"></i>
                </div>
                <span class="text-lg font-bold" :class="darkMode ? 'text-white' : 'text-gray-900'">MedVerse</span>
            </a>
            <button @click="mobileMenuOpen = false" class="lg:hidden p-2 rounded-lg"
                    :class="darkMode ? 'hover:bg-gray-700 text-gray-300' : 'hover:bg-gray-100 text-gray-600'">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="p-4 space-y-1 overflow-y-auto h-[calc(100%-4rem)]">
            @foreach($menuItems as $item)
            <a href="{{ $item['path'] }}"
               class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all font-medium
                      {{ Request::is(ltrim($item['path'], '/')) || Request::is(ltrim($item['path'], '/').'/*')
                         ? 'bg-teal-500 text-white shadow-lg shadow-teal-500/25'
                         : ($loop->first && Request::is(ltrim($item['path'], '/'))
                            ? 'bg-teal-500 text-white shadow-lg shadow-teal-500/25'
                            : '') }}"
               :class="'{{ Request::path() }}' === '{{ ltrim($item['path'], '/') }}'
                   ? ''
                   : darkMode ? 'text-gray-300 hover:bg-gray-700' : 'text-gray-600 hover:bg-gray-50'">
                <i class="{{ $item['icon'] }} w-5 text-center"></i>
                <span>{{ $item['label'] }}</span>
            </a>
            @endforeach
        </nav>
    </aside>

    {{-- Main Content --}}
    <div class="flex-1 transition-all duration-300" :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-0'">

        {{-- Top Navbar --}}
        <header class="h-16 sticky top-0 z-30 shadow-sm"
                :class="darkMode ? 'bg-gray-800' : 'bg-white'">
            <div class="h-full flex items-center justify-between px-4">
                <div class="flex items-center space-x-4">
                    <button @click="toggleSidebar()"
                            class="p-2 rounded-lg"
                            :class="darkMode ? 'hover:bg-gray-700 text-gray-300' : 'hover:bg-gray-100 text-gray-600'">
                        <i class="fa-solid fa-bars w-5 h-5"></i>
                    </button>
                    <h1 class="text-lg font-semibold" :class="darkMode ? 'text-white' : 'text-gray-900'">
                        @yield('page-title', 'Dashboard')
                    </h1>
                </div>

                <div class="flex items-center space-x-2">
                    {{-- Dark Mode Toggle --}}
                    <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)"
                            class="p-2 rounded-lg"
                            :class="darkMode ? 'hover:bg-gray-700 text-gray-300' : 'hover:bg-gray-100 text-gray-600'">
                        <i :class="darkMode ? 'fa-solid fa-sun' : 'fa-solid fa-moon'"></i>
                    </button>

                    {{-- Notifications --}}
                    <button class="relative p-2 rounded-lg"
                            :class="darkMode ? 'hover:bg-gray-700 text-gray-300' : 'hover:bg-gray-100 text-gray-600'">
                        <i class="fa-solid fa-bell"></i>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    {{-- User Menu --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="flex items-center space-x-2 p-2 rounded-lg"
                                :class="darkMode ? 'hover:bg-gray-700' : 'hover:bg-gray-100'">
                            <div class="w-8 h-8 rounded-full bg-teal-500 flex items-center justify-center">
                                <span class="text-white text-sm font-medium">
                                    {{ strtoupper(substr(Auth::user()->full_name ?? 'U', 0, 1)) }}
                                </span>
                            </div>
                            <span class="hidden sm:block font-medium"
                                  :class="darkMode ? 'text-white' : 'text-gray-900'">
                                {{ Auth::user()->full_name ?? 'User' }}
                            </span>
                            <i class="fa-solid fa-chevron-down text-xs"
                               :class="darkMode ? 'text-gray-400' : 'text-gray-500'"></i>
                        </button>

                        <div x-show="open" x-cloak @click.outside="open = false"
                             class="absolute right-0 mt-2 w-48 rounded-xl shadow-lg py-2 border z-50"
                             :class="darkMode ? 'bg-gray-700 border-gray-600' : 'bg-white border-gray-100'">
                            <div class="px-4 py-2 border-b" :class="darkMode ? 'border-gray-600' : 'border-gray-100'">
                                <p class="text-sm font-medium" :class="darkMode ? 'text-white' : 'text-gray-900'">
                                    {{ Auth::user()->full_name ?? 'User' }}
                                </p>
                                <p class="text-xs" :class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                    {{ Auth::user()->email ?? '' }}
                                </p>
                            </div>
                            <a href="{{ route('dashboard.profile') }}"
                               class="flex items-center space-x-2 px-4 py-2"
                               :class="darkMode ? 'hover:bg-gray-600 text-gray-300' : 'hover:bg-gray-50 text-gray-700'">
                                <i class="fa-solid fa-user-circle w-4"></i>
                                <span>Profile</span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="flex items-center space-x-2 px-4 py-2 w-full text-left"
                                        :class="darkMode ? 'hover:bg-gray-600 text-red-400' : 'hover:bg-gray-50 text-red-600'">
                                    <i class="fa-solid fa-right-from-bracket w-4"></i>
                                    <span>Sign Out</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="p-4 lg:p-6">
            @if(session('success'))
                <div class="mb-4 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center space-x-2 fade-in">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-center space-x-2 fade-in">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.5/cdn.min.js" defer></script>
<script>
function adminLayout() {
    return {
        sidebarOpen: true,
        mobileMenuOpen: false,
        darkMode: localStorage.getItem('darkMode') === 'true',
        toggleSidebar() {
            if (window.innerWidth < 1024) {
                this.mobileMenuOpen = !this.mobileMenuOpen;
            } else {
                this.sidebarOpen = !this.sidebarOpen;
            }
        }
    }
}
</script>
@stack('scripts')
</body>
</html>
