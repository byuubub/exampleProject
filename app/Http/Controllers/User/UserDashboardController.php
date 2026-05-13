<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Bill;
use App\Models\Prescription;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $menuItems = $this->getMenuItems($user->role);

        if ($user->role === 'doctor') {
            $stats = [
                'todayAppointments'    => Appointment::where('doctor_id', $user->id)->whereDate('appointment_date', today())->count(),
                'totalPatients'        => Appointment::where('doctor_id', $user->id)->distinct('patient_id')->count('patient_id'),
                'pendingAppointments'  => Appointment::where('doctor_id', $user->id)->where('status', 'pending')->count(),
                'completedAppointments'=> Appointment::where('doctor_id', $user->id)->where('status', 'completed')->count(),
            ];
            $upcomingAppointments = Appointment::where('doctor_id', $user->id)
                ->whereDate('appointment_date', '>=', today())
                ->with('patient')
                ->orderBy('appointment_date')
                ->limit(5)
                ->get();
        } else {
            $stats = [
                'totalAppointments'    => Appointment::where('patient_id', $user->id)->count(),
                'totalRecords'         => MedicalRecord::where('patient_id', $user->id)->count(),
                'unpaidBills'          => Bill::where('patient_id', $user->id)->where('status', 'pending')->count(),
                'activePrescriptions'  => Prescription::where('patient_id', $user->id)->where('status', 'active')->count(),
            ];
            $upcomingAppointments = Appointment::where('patient_id', $user->id)
                ->whereDate('appointment_date', '>=', today())
                ->with('doctor')
                ->orderBy('appointment_date')
                ->limit(5)
                ->get();
        }

        return view('user.dashboard', compact('stats', 'menuItems', 'upcomingAppointments'));
    }

    public function getMenuItems(string $role): array
    {
        if ($role === 'doctor') {
            return [
                ['icon' => 'fa-solid fa-gauge',          'label' => 'Dashboard',    'path' => '/dashboard'],
                ['icon' => 'fa-solid fa-calendar-day',   'label' => 'Today',        'path' => '/dashboard/today'],
                ['icon' => 'fa-solid fa-clock',          'label' => 'My Schedule',  'path' => '/dashboard/schedule'],
                ['icon' => 'fa-solid fa-prescription',   'label' => 'Prescriptions','path' => '/dashboard/prescriptions'],
                ['icon' => 'fa-solid fa-user-circle',    'label' => 'Profile',      'path' => '/dashboard/profile'],
            ];
        }

        return [
            ['icon' => 'fa-solid fa-gauge',                   'label' => 'Dashboard',       'path' => '/dashboard'],
            ['icon' => 'fa-solid fa-calendar-check',          'label' => 'Appointments',    'path' => '/dashboard/appointments'],
            ['icon' => 'fa-solid fa-file-medical',            'label' => 'Medical Records', 'path' => '/dashboard/medical-records'],
            ['icon' => 'fa-solid fa-receipt',                 'label' => 'Bills',           'path' => '/dashboard/bills'],
            ['icon' => 'fa-solid fa-prescription-bottle-alt', 'label' => 'Prescriptions',   'path' => '/dashboard/prescriptions'],
            ['icon' => 'fa-solid fa-user-circle',             'label' => 'Profile',         'path' => '/dashboard/profile'],
        ];
    }
}
