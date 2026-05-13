<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Bill;
use App\Models\Queue;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Build menu based on role
        $menuItems = $this->getMenuItems($user->role);

        $stats = [
            'totalHospitals'    => Hospital::count(),
            'totalUsers'        => User::count(),
            'totalDoctors'      => Doctor::count(),
            'totalPatients'     => Patient::count(),
            'todayVisits'       => Appointment::whereDate('appointment_date', today())->count(),
            'monthlyRevenue'    => Bill::where('status', 'paid')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
            'activeQueues'      => Queue::where('status', 'waiting')->count(),
            'pendingPayments'   => Bill::where('status', 'pending')->count(),
            'revenueByHospital' => $this->getRevenueByHospital(),
            'visitsByMonth'     => $this->getVisitsByMonth(),
        ];

        return view('admin.dashboard', compact('stats', 'menuItems'));
    }

    public function getMenuItemsPublic(string $role): array
    {
        return $this->getMenuItems($role);
    }

    private function getMenuItems(string $role): array
    {
        return match ($role) {
            'super_admin' => [
                ['icon' => 'fa-solid fa-gauge',           'label' => 'Dashboard',       'path' => '/admin'],
                ['icon' => 'fa-solid fa-hospital',        'label' => 'Hospitals',       'path' => '/admin/hospitals'],
                ['icon' => 'fa-solid fa-stethoscope',     'label' => 'Specializations', 'path' => '/admin/specializations'],
                ['icon' => 'fa-solid fa-users',           'label' => 'Users',           'path' => '/admin/users'],
                ['icon' => 'fa-solid fa-file-lines',      'label' => 'Reports',         'path' => '/admin/reports'],
                ['icon' => 'fa-solid fa-gear',            'label' => 'Settings',        'path' => '/admin/settings'],
            ],
            'admin_rs' => [
                ['icon' => 'fa-solid fa-gauge',           'label' => 'Dashboard',    'path' => '/admin'],
                ['icon' => 'fa-solid fa-stethoscope',     'label' => 'Doctors',      'path' => '/admin/doctors'],
                ['icon' => 'fa-solid fa-users',           'label' => 'Staff',        'path' => '/admin/staff'],
                ['icon' => 'fa-solid fa-calendar',        'label' => 'Schedules',    'path' => '/admin/schedules'],
                ['icon' => 'fa-solid fa-clipboard-list',  'label' => 'Patients',     'path' => '/admin/patients'],
                ['icon' => 'fa-solid fa-dollar-sign',     'label' => 'Revenue',      'path' => '/admin/revenue'],
                ['icon' => 'fa-solid fa-chart-line',      'label' => 'Visits',       'path' => '/admin/visits'],
            ],
            default => [
                ['icon' => 'fa-solid fa-gauge',           'label' => 'Dashboard',     'path' => '/staff'],
                ['icon' => 'fa-solid fa-user-plus',       'label' => 'Registration',  'path' => '/staff/registration'],
                ['icon' => 'fa-solid fa-clock',           'label' => 'Queue',         'path' => '/staff/queue'],
                ['icon' => 'fa-solid fa-calendar',        'label' => 'Appointments',  'path' => '/staff/appointments'],
                ['icon' => 'fa-solid fa-credit-card',     'label' => 'Payment',       'path' => '/staff/payment'],
                ['icon' => 'fa-solid fa-receipt',         'label' => 'Transactions',  'path' => '/staff/transactions'],
            ],
        };
    }

    private function getRevenueByHospital(): array
{
    return Hospital::query()
        ->select('hospitals.*')
        ->selectSub(function ($query) {
            $query->from('bills')
                ->join('appointments', 'appointments.id', '=', 'bills.appointment_id')
                ->join('doctors', 'doctors.id', '=', 'appointments.doctor_id')
                ->whereColumn('doctors.hospital_id', 'hospitals.id')
                ->where('bills.status', 'paid')
                ->selectRaw('SUM(bills.amount)');
        }, 'revenue')
        ->orderByDesc('revenue')
        ->limit(5)
        ->get()
        ->map(fn($hospital) => [
            'name' => $hospital->name,
            'revenue' => (float) ($hospital->revenue ?? 0),
        ])
        ->toArray();
}

    private function getVisitsByMonth(): array
    {
        return collect(range(0, 5))->map(function ($i) {
            $date = now()->subMonths($i);
            return [
                'month' => $date->format('M'),
                'count' => Appointment::whereYear('appointment_date', $date->year)
                    ->whereMonth('appointment_date', $date->month)
                    ->count(),
            ];
        })->toArray();
    }
}
