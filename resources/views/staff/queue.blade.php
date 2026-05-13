@extends('layouts.admin')

@section('title', 'Queue Management')
@section('page-title', 'Queue Management')

@section('content')

<div class="space-y-6 fade-in" x-data="queueManagement()" x-init="startAutoRefresh()">

    {{-- Current Serving --}}
    <div class="bg-gradient-to-r from-teal-500 to-cyan-500 rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-teal-100 text-sm font-medium mb-1">Now Serving</p>
                @if($currentQueue)
                <h2 class="text-3xl font-bold mb-1">No. {{ str_pad($currentQueue->queue_number, 3, '0', STR_PAD_LEFT) }}</h2>
                <p class="text-teal-100">{{ $currentQueue->patient->full_name ?? 'Patient' }}</p>
                @else
                <h2 class="text-3xl font-bold">—</h2>
                <p class="text-teal-100">No patient being served</p>
                @endif
            </div>
            <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center">
                <i class="fa-solid fa-person-walking-arrow-right text-3xl"></i>
            </div>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm text-center">
            <p class="text-2xl font-bold text-amber-500">{{ $waitingCount }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Waiting</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm text-center">
            <p class="text-2xl font-bold text-teal-500">{{ $inProgressCount }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">In Progress</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm text-center">
            <p class="text-2xl font-bold text-green-500">{{ $completedCount }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Completed</p>
        </div>
    </div>

    {{-- Queue List --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b dark:border-gray-700">
            <h3 class="font-semibold text-gray-900 dark:text-white">Queue List</h3>
            <button onclick="window.location.reload()"
                    class="flex items-center space-x-2 px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 text-sm transition-colors">
                <i class="fa-solid fa-rotate-right"></i>
                <span>Refresh</span>
            </button>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($queues as $queue)
            <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl font-bold text-lg flex items-center justify-center
                                {{ $queue->status === 'waiting' ? 'bg-amber-100 text-amber-600' :
                                   ($queue->status === 'called' || $queue->status === 'in_progress' ? 'bg-teal-100 text-teal-600' :
                                    ($queue->status === 'completed' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-500')) }}">
                        {{ str_pad($queue->queue_number, 3, '0', STR_PAD_LEFT) }}
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $queue->patient->full_name ?? 'Patient' }}</p>
                        <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                            @if($queue->doctor)
                            <span>Dr. {{ $queue->doctor->user->full_name ?? '' }}</span>
                            <span>·</span>
                            @endif
                            <span>{{ \Carbon\Carbon::parse($queue->created_at)->format('H:i') }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                 {{ $queue->status === 'waiting' ? 'bg-amber-100 text-amber-700' :
                                    ($queue->status === 'called' ? 'bg-blue-100 text-blue-700' :
                                     ($queue->status === 'in_progress' ? 'bg-teal-100 text-teal-700' :
                                      ($queue->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'))) }}">
                        {{ ucfirst(str_replace('_', ' ', $queue->status)) }}
                    </span>
                    @if($queue->status === 'waiting')
                    <form method="POST" action="{{ route('staff.queue.call', $queue->id) }}" class="inline">
                        @csrf
                        <button type="submit" class="p-2 rounded-lg bg-teal-500 text-white hover:bg-teal-600 transition-colors" title="Call Patient">
                            <i class="fa-solid fa-play text-sm"></i>
                        </button>
                    </form>
                    @elseif(in_array($queue->status, ['called', 'in_progress']))
                    <form method="POST" action="{{ route('staff.queue.complete', $queue->id) }}" class="inline">
                        @csrf
                        <button type="submit" class="p-2 rounded-lg bg-green-500 text-white hover:bg-green-600 transition-colors" title="Mark Complete">
                            <i class="fa-solid fa-check text-sm"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="py-16 text-center text-gray-400">
                <i class="fa-solid fa-clock text-4xl mb-3"></i>
                <p class="font-medium">No queues today</p>
            </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function queueManagement() {
    return {
        startAutoRefresh() {
            setInterval(() => window.location.reload(), 30000);
        }
    }
}
</script>
@endpush
