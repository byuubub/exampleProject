@extends('layouts.admin')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')

<div class="max-w-2xl mx-auto space-y-6 fade-in">

    {{-- Avatar Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm text-center">
        <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-teal-400 to-cyan-500 flex items-center justify-center mx-auto mb-4">
            <span class="text-white text-4xl font-bold">
                {{ strtoupper(substr(Auth::user()->full_name ?? 'U', 0, 1)) }}
            </span>
        </div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ Auth::user()->full_name }}</h2>
        <p class="text-teal-600 dark:text-teal-400">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</p>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">{{ Auth::user()->email }}</p>
    </div>

    {{-- Edit Form --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Edit Profile</h3>

        @if(session('success'))
        <div class="mb-5 p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">
            <i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('dashboard.profile.update') }}" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Full Name *</label>
                <input type="text" name="full_name" value="{{ old('full_name', Auth::user()->full_name) }}" required
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none @error('full_name') border-red-400 @enderror">
                @error('full_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email *</label>
                <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none @error('email') border-red-400 @enderror">
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', Auth::user()->phone) }}"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Address</label>
                <textarea name="address" rows="3"
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none resize-none">{{ old('address', Auth::user()->address) }}</textarea>
            </div>

            <hr class="border-gray-100 dark:border-gray-700">

            <h4 class="font-medium text-gray-900 dark:text-white">Change Password</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400 -mt-3">Leave blank to keep your current password.</p>

            <div x-data="{ show: false }">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">New Password</label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" name="password"
                           class="w-full px-4 pr-12 py-3 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none"
                           placeholder="Minimum 8 characters">
                    <button type="button" @click="show = !show"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i :class="show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
                    </button>
                </div>
                @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Confirm Password</label>
                <input type="password" name="password_confirmation"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none"
                       placeholder="Repeat new password">
            </div>

            <button type="submit"
                    class="w-full py-3 rounded-xl bg-gradient-to-r from-teal-500 to-teal-600 text-white font-semibold hover:from-teal-600 hover:to-teal-700 transition-all shadow-lg shadow-teal-500/25">
                <i class="fa-solid fa-floppy-disk mr-2"></i> Save Changes
            </button>
        </form>
    </div>
</div>
@endsection
