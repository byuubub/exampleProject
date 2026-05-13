<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentManagementController extends Controller
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

    public function index(Request $request)
    {
        $hospitalId = Auth::user()->hospital_id;

        $query = Appointment::with(['patient.user', 'doctor.user', 'doctor.specialization'])
            ->when($hospitalId, fn($q) => $q->whereHas('doctor', fn($d) => $d->where('hospital_id', $hospitalId)));

        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        } else {
            $query->whereDate('appointment_date', today());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $appointments = $query->orderBy('appointment_time')->paginate(20);
        $menuItems    = $this->menuItems();

        return view('staff.appointments', compact('appointments', 'menuItems'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'status' => 'required|in:scheduled,confirmed,in_progress,completed,cancelled',
            'notes'  => 'nullable|string',
        ]);

        $appointment->update($data);

        return back()->with('success', 'Appointment status updated.');
    }
}
