@extends('layouts.admin')

@section('title', 'Staff Dashboard')
@section('page-title', 'Staff Dashboard')

@section('content')

<div class="space-y-6 fade-in">

    <div class="bg-gradient-to-r from-teal-500 to-cyan-500 rounded-2xl p-6 text-white">
        <h2 class="text-2xl font-bold mb-1">Good {{ now()->format('A') === 'AM' ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening') }}, {{ explode(' ', Auth::user()->full_name ?? 'Staff')[0] }}! 👋</h2>
        <p class="text-teal-100">{{ now()->format('l, d F Y') }} · {{ Auth::user()->hospital->name ?? 'MedVerse' }}</p>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label' => "Today's Queue",  'value' => $todayQueue ?? 0,        'icon' => 'fa-clock',            'color' => 'teal',   'link' => route('staff.queue')],
            ['label' => 'Appointments',   'value' => $todayAppointments ?? 0, 'icon' => 'fa-calendar-check',  'color' => 'blue',   'link' => route('staff.appointments')],
            ['label' => 'Pending Reg.',   'value' => $pendingRegistrations ?? 0,'icon' => 'fa-user-plus',     'color' => 'amber',  'link' => route('staff.registration')],
            ['label' => 'Pending Payment','value' => $pendingPayments ?? 0,   'icon' => 'fa-credit-card',      'color' => 'green',  'link' => route('staff.payment')],
        ] as $stat)
        <a href="{{ $stat['link'] }}"
           class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all block">
            <div class="w-10 h-10 rounded-lg bg-{{ $stat['color'] }}-100 flex items-center justify-center mb-3">
                <i class="fa-solid {{ $stat['icon'] }} text-{{ $stat['color'] }}-500"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stat['value'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</p>
        </a>
        @endforeach
    </div>

    {{-- Quick Actions --}}
    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
            <div class="space-y-3">
                @foreach([
                    ['href' => route('staff.registration'), 'icon' => 'fa-user-plus', 'label' => 'Register New Patient', 'color' => 'teal'],
                    ['href' => route('staff.queue'), 'icon' => 'fa-play', 'label' => 'Manage Queue', 'color' => 'blue'],
                    ['href' => route('staff.payment'), 'icon' => 'fa-credit-card', 'label' => 'Process Payment', 'color' => 'green'],
                    ['href' => route('staff.transactions'), 'icon' => 'fa-receipt', 'label' => 'View Transactions', 'color' => 'purple'],
                ] as $action)
                <a href="{{ $action['href'] }}"
                   class="flex items-center space-x-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="w-9 h-9 rounded-lg bg-{{ $action['color'] }}-100 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid {{ $action['icon'] }} text-{{ $action['color'] }}-500 text-sm"></i>
                    </div>
                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ $action['label'] }}</span>
                    <i class="fa-solid fa-chevron-right text-gray-300 ml-auto text-xs"></i>
                </a>
                @endforeach
            </div>
        </div>

        {{-- Today's Queue Preview --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900 dark:text-white">Queue Preview</h3>
                <a href="{{ route('staff.queue') }}" class="text-sm text-teal-600 hover:text-teal-700">View all</a>
            </div>
            @if($todayQueues && $todayQueues->count() > 0)
            <div class="space-y-3">
                @foreach($todayQueues->take(5) as $queue)
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-600 text-sm font-bold flex items-center justify-center">
                            {{ str_pad($queue->queue_number, 3, '0', STR_PAD_LEFT) }}
                        </div>
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $queue->patient->full_name ?? 'Patient' }}</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                 {{ $queue->status === 'waiting' ? 'bg-amber-100 text-amber-700' :
                                    ($queue->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-teal-100 text-teal-700') }}">
                        {{ ucfirst($queue->status) }}
                    </span>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8 text-gray-400 text-sm">
                <i class="fa-solid fa-clock text-2xl mb-2"></i>
                <p>No queue today</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
