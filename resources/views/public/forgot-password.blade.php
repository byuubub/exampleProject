@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-teal-50 via-white to-cyan-50 flex items-center justify-center p-8">
    <div class="w-full max-w-md fade-in">
        <a href="{{ route('login') }}" class="inline-flex items-center text-gray-600 hover:text-teal-600 mb-8 transition-colors">
            <i class="fa-solid fa-arrow-left mr-2"></i>Back to Sign In
        </a>
        <div class="bg-white rounded-3xl shadow-xl p-8">
            <div class="text-center mb-8">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-teal-500 to-teal-600 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-lock-open text-white text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Forgot Password?</h1>
                <p class="text-gray-500">Enter your email and we'll send you a reset link.</p>
            </div>

            @if(session('status'))
            <div class="mb-5 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm text-center">
                <i class="fa-solid fa-envelope-circle-check mr-2"></i>{{ session('status') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                    <div class="relative">
                        <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none"
                               placeholder="you@example.com">
                    </div>
                </div>
                <button type="submit"
                        class="w-full py-3 rounded-xl bg-gradient-to-r from-teal-500 to-teal-600 text-white font-semibold hover:from-teal-600 hover:to-teal-700 transition-all shadow-lg shadow-teal-500/25">
                    Send Reset Link
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
