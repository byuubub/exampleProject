@extends('layouts.admin')

@section('title', 'Online Payment')
@section('page-title', 'Online Payment')

@section('content')

<div class="max-w-lg mx-auto space-y-6 fade-in">

    {{-- Bill Summary --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-teal-500 to-cyan-500 p-6 text-white">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <i class="fa-solid fa-file-invoice-dollar text-xl"></i>
                </div>
                <div>
                    <p class="text-teal-100 text-sm">Invoice</p>
                    <p class="font-bold text-lg">INV-{{ str_pad($bill->id, 6, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>
            <div class="text-3xl font-bold mb-1">Rp {{ number_format($bill->amount, 0, ',', '.') }}</div>
            <p class="text-teal-100 text-sm">{{ \Carbon\Carbon::parse($bill->created_at)->format('d F Y') }}</p>
        </div>

        <div class="p-6 space-y-4">
            <div class="flex justify-between text-sm">
                <span class="text-gray-500 dark:text-gray-400">Description</span>
                <span class="text-gray-900 dark:text-white font-medium">{{ $bill->description ?? 'Medical consultation' }}</span>
            </div>
            @if($bill->appointment)
            <div class="flex justify-between text-sm">
                <span class="text-gray-500 dark:text-gray-400">Doctor</span>
                <span class="text-gray-900 dark:text-white">Dr. {{ $bill->appointment->doctor->user->full_name ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-500 dark:text-gray-400">Visit Date</span>
                <span class="text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($bill->appointment->appointment_date)->format('d M Y') }}</span>
            </div>
            @endif
            <div class="flex justify-between text-sm">
                <span class="text-gray-500 dark:text-gray-400">Status</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Pending Payment</span>
            </div>
            <div class="pt-3 border-t dark:border-gray-700 flex justify-between font-bold">
                <span class="text-gray-900 dark:text-white">Total Amount</span>
                <span class="text-teal-600 dark:text-teal-400 text-lg">Rp {{ number_format($bill->amount, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Payment Method --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm" x-data="{ method: 'transfer' }">
        <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Select Payment Method</h3>
        <div class="space-y-3 mb-6">
            @foreach([
                ['value' => 'transfer', 'label' => 'Bank Transfer', 'icon' => 'fa-building-columns', 'desc' => 'Transfer via ATM or internet banking'],
                ['value' => 'va', 'label' => 'Virtual Account', 'icon' => 'fa-hashtag', 'desc' => 'Pay via any bank VA'],
                ['value' => 'ewallet', 'label' => 'E-Wallet', 'icon' => 'fa-wallet', 'desc' => 'GoPay, OVO, Dana, ShopeePay'],
            ] as $pm)
            <label class="flex items-center space-x-3 p-4 rounded-xl border-2 cursor-pointer transition-colors"
                   :class="method === '{{ $pm['value'] }}' ? 'border-teal-500 bg-teal-50 dark:bg-teal-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300'">
                <input type="radio" name="payment_method" value="{{ $pm['value'] }}" x-model="method" class="sr-only">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                     :class="method === '{{ $pm['value'] }}' ? 'bg-teal-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-500'">
                    <i class="fa-solid {{ $pm['icon'] }}"></i>
                </div>
                <div class="flex-1">
                    <p class="font-medium text-gray-900 dark:text-white">{{ $pm['label'] }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $pm['desc'] }}</p>
                </div>
                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0"
                     :class="method === '{{ $pm['value'] }}' ? 'border-teal-500' : 'border-gray-300'">
                    <div class="w-2.5 h-2.5 rounded-full bg-teal-500"
                         x-show="method === '{{ $pm['value'] }}'"></div>
                </div>
            </label>
            @endforeach
        </div>

        <form method="POST" action="{{ route('dashboard.payment.process', $bill->id) }}">
            @csrf
            <input type="hidden" name="payment_method" :value="method">
            <button type="submit"
                    class="w-full py-4 rounded-xl bg-gradient-to-r from-teal-500 to-teal-600 text-white font-semibold hover:from-teal-600 hover:to-teal-700 transition-all shadow-lg shadow-teal-500/25">
                <i class="fa-solid fa-lock mr-2"></i>
                Pay Rp {{ number_format($bill->amount, 0, ',', '.') }}
            </button>
        </form>

        <p class="text-center text-xs text-gray-400 mt-4">
            <i class="fa-solid fa-shield-halved mr-1"></i>
            Your payment is secured and encrypted
        </p>
    </div>

    <a href="{{ route('dashboard.bills') }}" class="flex items-center justify-center text-sm text-gray-500 hover:text-teal-600 transition-colors">
        <i class="fa-solid fa-arrow-left mr-2"></i> Back to Bills
    </a>
</div>
@endsection
