<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Bill;
use App\Models\Patient;
use App\Models\Queue;
use Illuminate\Support\Facades\Auth;

class StaffDashboardController extends Controller
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
        $user        = Auth::user();
        $hospitalId  = $user->hospital_id;

        $todayQueue = Queue::whereDate('queue_date', today())
            ->when($hospitalId, fn($q) => $q->whereHas('doctor', fn($d) => $d->where('hospital_id', $hospitalId)))
            ->count();

        $todayAppointments = Appointment::whereDate('appointment_date', today())
            ->when($hospitalId, fn($q) => $q->whereHas('doctor', fn($d) => $d->where('hospital_id', $hospitalId)))
            ->count();

        $pendingRegistrations = Patient::whereDate('created_at', today())->count();

        $pendingPayments = Bill::where('status', 'pending')
            ->when($hospitalId, fn($q) => $q->whereHas('appointment.doctor', fn($d) => $d->where('hospital_id', $hospitalId)))
            ->count();

        $todayQueues = Queue::with(['patient.user', 'doctor.user'])
            ->whereDate('queue_date', today())
            ->orderBy('queue_number')
            ->limit(10)
            ->get();

        $menuItems = $this->menuItems();

        return view('staff.dashboard', compact(
            'todayQueue', 'todayAppointments', 'pendingRegistrations',
            'pendingPayments', 'todayQueues', 'menuItems'
        ));
    }
}
