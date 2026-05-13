@extends('layouts.admin')

@section('title', 'Cash Payment')
@section('page-title', 'Cash Payment')

@section('content')

<div class="space-y-6 fade-in" x-data="cashPayment()">

    {{-- Success State --}}
    <div x-show="success" x-cloak class="bg-white dark:bg-gray-800 rounded-2xl p-10 shadow-sm text-center">
        <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-5">
            <i class="fa-solid fa-circle-check text-green-500 text-4xl"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Payment Successful!</h3>
        <p class="text-gray-500 dark:text-gray-400 mb-6">Transaction has been recorded.</p>
        <div class="flex justify-center space-x-3">
            <button @click="success = false; selectedBill = null; amount = ''"
                    class="px-5 py-2.5 rounded-xl bg-teal-500 text-white font-medium hover:bg-teal-600">
                Process Another
            </button>
            <a href="{{ route('staff.transactions') }}"
               class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50">
                View Transactions
            </a>
        </div>
    </div>

    {{-- Main Content --}}
    <div x-show="!success" class="grid lg:grid-cols-2 gap-6">

        {{-- Unpaid Bills List --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b dark:border-gray-700">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" x-model="search" placeholder="Search patient or bill..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 outline-none text-sm">
                </div>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-96 overflow-y-auto">
                @forelse($unpaidBills as $bill)
                <div class="p-4 cursor-pointer transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50"
                     :class="selectedBill && selectedBill.id === {{ $bill->id }} ? 'bg-teal-50 dark:bg-teal-900/20 border-l-4 border-teal-500' : ''"
                     @click="selectBill({{ $bill->toJson() }})"
                     x-show="!search || '{{ strtolower($bill->patient->user->full_name ?? '') }} {{ $bill->id }}'.includes(search.toLowerCase())">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-receipt text-amber-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white text-sm">
                                    {{ $bill->patient->user->full_name ?? 'Patient' }}
                                </p>
                                <p class="text-xs text-gray-500">INV-{{ str_pad($bill->id, 6, '0', STR_PAD_LEFT) }}</p>
                            </div>
                        </div>
                        <p class="font-semibold text-gray-900 dark:text-white text-sm">
                            Rp {{ number_format($bill->amount, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center text-gray-400">
                    <i class="fa-solid fa-circle-check text-3xl mb-2 text-green-400"></i>
                    <p>No pending bills</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Payment Panel --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
            <template x-if="!selectedBill">
                <div class="h-full flex flex-col items-center justify-center py-12 text-gray-400">
                    <i class="fa-solid fa-hand-pointer text-4xl mb-3"></i>
                    <p class="font-medium">Select a bill to process payment</p>
                </div>
            </template>

            <template x-if="selectedBill">
                <div class="space-y-5">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Process Payment</h3>

                    {{-- Bill Summary --}}
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Patient</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="selectedBill.patient_name"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Invoice</span>
                            <span class="font-mono text-gray-600 dark:text-gray-400" x-text="`INV-${String(selectedBill.id).padStart(6,'0')}`"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Description</span>
                            <span class="text-gray-600 dark:text-gray-400" x-text="selectedBill.description || 'Medical consultation'"></span>
                        </div>
                        <div class="border-t dark:border-gray-600 pt-3 flex justify-between font-bold">
                            <span class="text-gray-900 dark:text-white">Total Due</span>
                            <span class="text-teal-600 dark:text-teal-400 text-lg"
                                  x-text="'Rp ' + Number(selectedBill.amount).toLocaleString('id-ID')"></span>
                        </div>
                    </div>

                    {{-- Amount Received --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                            Cash Received (Rp)
                        </label>
                        <input type="number" x-model="amount"
                               :placeholder="selectedBill.amount"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 outline-none text-lg font-semibold">
                    </div>

                    {{-- Change --}}
                    <div x-show="amount && parseFloat(amount) >= parseFloat(selectedBill.amount)"
                         class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400 font-medium">Change</span>
                            <span class="text-green-600 font-bold text-lg"
                                  x-text="'Rp ' + (parseFloat(amount || 0) - parseFloat(selectedBill.amount)).toLocaleString('id-ID')"></span>
                        </div>
                    </div>

                    {{-- Insufficient warning --}}
                    <div x-show="amount && parseFloat(amount) < parseFloat(selectedBill.amount)"
                         class="bg-red-50 dark:bg-red-900/20 rounded-xl p-4 flex items-center space-x-2 text-red-600">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span class="text-sm font-medium">Amount is less than the total due</span>
                    </div>

                    <form method="POST" action="{{ route('staff.payment.process') }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="bill_id" :value="selectedBill.id">
                        <input type="hidden" name="amount" :value="selectedBill.amount">
                        <input type="hidden" name="cash_received" :value="amount">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Payment Method</label>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach(['cash' => ['icon' => 'fa-money-bill', 'label' => 'Cash'], 'qris' => ['icon' => 'fa-qrcode', 'label' => 'QRIS'], 'debit' => ['icon' => 'fa-credit-card', 'label' => 'Debit'], 'insurance' => ['icon' => 'fa-shield-halved', 'label' => 'Insurance']] as $val => $opt)
                                <label class="flex items-center space-x-2 p-2.5 rounded-lg border border-gray-200 dark:border-gray-600 cursor-pointer hover:border-teal-400 transition-colors has-[:checked]:border-teal-500 has-[:checked]:bg-teal-50 dark:has-[:checked]:bg-teal-900/20">
                                    <input type="radio" name="payment_method" value="{{ $val }}" {{ $val === 'cash' ? 'checked' : '' }} class="text-teal-500">
                                    <i class="fa-solid {{ $opt['icon'] }} text-gray-400 text-sm"></i>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $opt['label'] }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit"
                                :disabled="!amount || parseFloat(amount) < parseFloat(selectedBill.amount)"
                                class="w-full py-3.5 rounded-xl bg-teal-500 text-white font-semibold hover:bg-teal-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fa-solid fa-circle-check mr-2"></i> Confirm Payment
                        </button>
                    </form>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function cashPayment() {
    return {
        search: '',
        selectedBill: null,
        amount: '',
        success: {{ session('success') ? 'true' : 'false' }},
        selectBill(bill) {
            this.selectedBill = { ...bill, patient_name: bill.patient?.user?.full_name ?? 'Patient' };
            this.amount = '';
        }
    }
}
</script>
@endpush
