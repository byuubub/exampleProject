@extends('layouts.app')

@section('title', 'Sign In')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-teal-50 via-white to-cyan-50 flex">

    {{-- Left Side - Form --}}
    <div class="flex-1 flex items-center justify-center p-8">
        <div class="w-full max-w-md fade-in">
            <a href="{{ url('/') }}" class="inline-flex items-center text-gray-600 hover:text-teal-600 mb-8 transition-colors">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                Back to Home
            </a>

            <div class="mb-8">
                <div class="flex items-center space-x-2 mb-4">
                    <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-stethoscope text-white"></i>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-teal-600 to-cyan-600 bg-clip-text text-transparent">
                        MedVerse
                    </span>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome back</h1>
                <p class="text-gray-600">Sign in to your account to continue</p>
            </div>

            @if($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
                {{ $errors->first() }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
                {{ session('error') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <div class="relative">
                        <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="email" name="email" required
                               value="{{ old('email') }}"
                               class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition-all @error('email') border-red-400 @enderror"
                               placeholder="you@example.com">
                    </div>
                </div>

                <div x-data="{ show: false }">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input :type="show ? 'text' : 'password'" name="password" required
                               class="w-full pl-12 pr-12 py-3 rounded-xl border border-gray-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none transition-all @error('password') border-red-400 @enderror"
                               placeholder="••••••••">
                        <button type="button" @click="show = !show"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i :class="show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-teal-500 focus:ring-teal-500">
                        <span class="ml-2 text-sm text-gray-600">Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-teal-600 hover:text-teal-700 font-medium">
                        Forgot password?
                    </a>
                </div>

                <button type="submit"
                        class="w-full py-3 rounded-xl bg-gradient-to-r from-teal-500 to-teal-600 text-white font-semibold hover:from-teal-600 hover:to-teal-700 transition-all shadow-lg shadow-teal-500/25">
                    Sign In
                </button>
            </form>

            <p class="mt-6 text-center text-gray-600">
                Don't have an account?
                <a href="{{ route('login') }}" class="text-teal-600 hover:text-teal-700 font-medium">
                    Contact administrator
                </a>
            </p>
        </div>
    </div>

    {{-- Right Side - Decorative --}}
    <div class="hidden lg:flex lg:w-1/2 relative">
        <div class="absolute inset-0 bg-gradient-to-br from-teal-500 via-teal-600 to-cyan-600"></div>
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="absolute inset-0 flex items-center justify-center p-12">
            <div class="text-white text-center">
                <div class="w-24 h-24 bg-white/20 rounded-3xl flex items-center justify-center mx-auto mb-8">
                    <i class="fa-solid fa-stethoscope text-5xl text-white"></i>
                </div>
                <h2 class="text-4xl font-bold mb-4">Your Health, Our Priority</h2>
                <p class="text-xl text-teal-100 max-w-md">
                    Access your medical records, book appointments, and manage your healthcare journey.
                </p>
            </div>
        </div>
    </div>

</div>
@endsection
