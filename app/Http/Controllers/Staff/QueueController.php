<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Queue;
use Illuminate\Support\Facades\Auth;

class QueueController extends Controller
{
    private function menuItems(): array
    {
        return [
            ['icon' => 'fa-solid fa-gauge',          'label' => 'Dashboard',     'path' => '/staff'],
            ['icon' => 'fa-solid fa-user-plus',      'label' => 'Registration',  'path' => '/staff/registration'],
            ['icon' => 'fa-solid fa-clock',          'label' => 'Queue',         'path' => '/staff/queue'],
            ['icon' => 'fa-solid fa-calendar-check', 'label' => 'Appointments',  'path' => '/staff/appointments'],
            ['icon' => 'fa-solid fa-credit-card',    'label' => 'Payment',       'path' => '/staff/payment'],
            ['icon' => 'fa-solid fa-receipt',        'label' => 'Transactions',  'path' => '/staff/transactions'],
        ];
    }

    public function index()
    {
        $hospitalId = Auth::user()->hospital_id;

        $queues = Queue::with(['patient.user', 'doctor.user'])
            ->whereDate('queue_date', today())
            ->when($hospitalId, fn($q) => $q->whereHas('doctor', fn($d) => $d->where('hospital_id', $hospitalId)))
            ->orderBy('queue_number')
            ->get();

        $currentQueue   = $queues->whereIn('status', ['called', 'in_progress'])->first();
        $waitingCount   = $queues->where('status', 'waiting')->count();
        $inProgressCount= $queues->whereIn('status', ['called', 'in_progress'])->count();
        $completedCount = $queues->where('status', 'completed')->count();

        $menuItems = $this->menuItems();

        return view('staff.queue', compact(
            'queues', 'currentQueue', 'waitingCount', 'inProgressCount', 'completedCount', 'menuItems'
        ));
    }

    public function call(Queue $queue)
    {
        // Mark any in-progress as completed first
        Queue::whereDate('queue_date', today())
            ->whereIn('status', ['called', 'in_progress'])
            ->update(['status' => 'in_progress']);

        $queue->update(['status' => 'called']);

        // Update appointment status
        $queue->appointment?->update(['status' => 'in_progress']);

        return back()->with('success', "Calling queue No. " . str_pad($queue->queue_number, 3, '0', STR_PAD_LEFT));
    }

    public function complete(Queue $queue)
    {
        $queue->update(['status' => 'completed']);
        $queue->appointment?->update(['status' => 'completed']);

        return back()->with('success', 'Queue completed.');
    }
}
