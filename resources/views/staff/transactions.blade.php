@extends('layouts.admin')

@section('title', 'Transaction History')
@section('page-title', 'Transaction History')

@section('content')

<div class="space-y-6 fade-in" x-data="{ search: '' }">

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label' => "Today's Revenue", 'value' => 'Rp '.number_format($todayRevenue ?? 0, 0, ',', '.'), 'icon' => 'fa-chart-line',  'color' => 'teal'],
            ['label' => 'Transactions',    'value' => $totalTransactions ?? 0,                              'icon' => 'fa-receipt',      'color' => 'blue'],
            ['label' => 'Cash Payments',   'value' => $cashCount ?? 0,                                      'icon' => 'fa-money-bill',   'color' => 'green'],
            ['label' => 'QRIS / Digital',  'value' => $digitalCount ?? 0,                                   'icon' => 'fa-qrcode',       'color' => 'purple'],
        ] as $stat)
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-{{ $stat['color'] }}-100 flex items-center justify-center">
                    <i class="fa-solid {{ $stat['icon'] }} text-{{ $stat['color'] }}-500"></i>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $stat['value'] }}</p>
                    <p class="text-xs text-gray-500">{{ $stat['label'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b dark:border-gray-700 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900 dark:text-white">All Transactions</h3>
            <div class="relative max-w-xs">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" x-model="search" placeholder="Search..."
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-teal-500 outline-none">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50 text-left">
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Invoice</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Patient</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Method</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Date</th>
                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors"
                        x-show="!search || '{{ strtolower($tx->bill->patient->user->full_name ?? '') }} {{ $tx->id }}'.includes(search.toLowerCase())">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900 dark:text-white text-sm">TRX-{{ str_pad($tx->id, 6, '0', STR_PAD_LEFT) }}</p>
                            <p class="text-xs text-gray-500 md:hidden">{{ $tx->bill->patient->user->full_name ?? '—' }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 hidden md:table-cell">
                            {{ $tx->bill->patient->user->full_name ?? '—' }}
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-900 dark:text-white">Rp {{ number_format($tx->amount, 0, ',', '.') }}</p>
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            @php
                                $methodMap = [
                                    'cash'          => ['icon' => '💵', 'label' => 'Cash'],
                                    'qris'          => ['icon' => '📱', 'label' => 'QRIS'],
                                    'debit'         => ['icon' => '💳', 'label' => 'Debit'],
                                    'bank_transfer' => ['icon' => '🏦', 'label' => 'Transfer'],
                                    'insurance'     => ['icon' => '🏥', 'label' => 'Insurance'],
                                ];
                                $method = $methodMap[$tx->payment_method] ?? ['icon' => '💳', 'label' => ucfirst($tx->payment_method ?? 'Other')];
                            @endphp
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $method['icon'] }} {{ $method['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 hidden lg:table-cell">
                            {{ $tx->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                Paid
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-gray-400">
                            <i class="fa-solid fa-receipt text-4xl mb-3"></i>
                            <p>No transactions found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
        <div class="px-6 py-4 border-t dark:border-gray-700">{{ $transactions->links() }}</div>
        @endif
    </div>
</div>
@endsection
