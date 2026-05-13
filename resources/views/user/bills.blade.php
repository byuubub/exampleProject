@extends('layouts.admin')

@section('title', 'My Bills')
@section('page-title', 'My Bills')

@section('content')

<div class="space-y-6 fade-in">

    {{-- Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                    <i class="fa-solid fa-clock text-amber-500"></i>
                </div>
                <div>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $pendingCount }}</p>
                    <p class="text-sm text-gray-500">Pending Bills</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                    <i class="fa-solid fa-money-bill text-red-500"></i>
                </div>
                <div>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</p>
                    <p class="text-sm text-gray-500">Total Unpaid</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                    <i class="fa-solid fa-circle-check text-green-500"></i>
                </div>
                <div>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $paidCount }}</p>
                    <p class="text-sm text-gray-500">Paid Bills</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Bills Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b dark:border-gray-700">
            <h3 class="font-semibold text-gray-900 dark:text-white">Billing History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-left">
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Invoice</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Date</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Description</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($bills as $bill)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900 dark:text-white">INV-{{ str_pad($bill->id, 6, '0', STR_PAD_LEFT) }}</p>
                            <p class="text-xs text-gray-500 md:hidden">{{ \Carbon\Carbon::parse($bill->created_at)->format('d M Y') }}</p>
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell text-sm text-gray-600 dark:text-gray-400">
                            {{ \Carbon\Carbon::parse($bill->created_at)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell text-sm text-gray-600 dark:text-gray-400">
                            {{ $bill->description ?? 'Medical consultation' }}
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-900 dark:text-white">Rp {{ number_format($bill->amount, 0, ',', '.') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                         {{ $bill->status === 'paid' ? 'bg-green-100 text-green-700' :
                                            ($bill->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600') }}">
                                {{ ucfirst($bill->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($bill->status === 'pending')
                            <a href="{{ route('dashboard.payment', $bill->id) }}"
                               class="inline-flex items-center px-3 py-1.5 rounded-lg bg-teal-500 text-white text-xs font-medium hover:bg-teal-600 transition-colors">
                                <i class="fa-solid fa-credit-card mr-1"></i> Pay Now
                            </a>
                            @else
                            <span class="text-sm text-gray-400">
                                <i class="fa-solid fa-circle-check text-green-500 mr-1"></i> Paid
                            </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-gray-400">
                            <i class="fa-solid fa-receipt text-4xl mb-3"></i>
                            <p>No bills found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bills->hasPages())
        <div class="px-6 py-4 border-t dark:border-gray-700">{{ $bills->links() }}</div>
        @endif
    </div>
</div>
@endsection
