<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Bill;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminReportsController extends Controller
{
    private function menuItems(): array
    {
        return (new AdminDashboardController)->getMenuItemsPublic(Auth::user()->role);
    }

    public function index(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());

        $totalVisits = Appointment::whereBetween('appointment_date', [$from, $to])->count();
        $totalRevenue = Bill::where('status', 'paid')->whereBetween('created_at', [$from, $to.' 23:59:59'])->sum('amount');
        $newPatients = Patient::whereBetween('created_at', [$from, $to.' 23:59:59'])->count();
        $totalAppointments = Appointment::whereBetween('appointment_date', [$from, $to])->count();

        $topDoctors = Doctor::with('user')
            ->withCount(['appointments as visit_count' => fn ($q) => $q->whereBetween('appointment_date', [$from, $to])])
            ->orderByDesc('visit_count')
            ->limit(5)
            ->get()
            ->map(fn ($d) => ['name' => $d->user->full_name ?? 'N/A', 'visits' => $d->visit_count])
            ->toArray();

        $revenueByHospital = Hospital::query()
            ->select('hospitals.*')
            ->selectSub(function ($query) use ($from, $to) {
                $query->from('bills')
                    ->join('appointments', 'appointments.id', '=', 'bills.appointment_id')
                    ->join('doctors', 'doctors.id', '=', 'appointments.doctor_id')
                    ->whereColumn('doctors.hospital_id', 'hospitals.id')
                    ->where('bills.status', 'paid')
                    ->whereBetween('bills.created_at', [$from, $to.' 23:59:59'])
                    ->selectRaw('SUM(bills.amount)');
            }, 'revenue')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get()
            ->map(fn ($hospital) => [
                'name' => $hospital->name,
                'revenue' => (float) ($hospital->revenue ?? 0),
            ])
            ->toArray();

        $recentAppointments = Appointment::with(['patient.user', 'doctor.user', 'doctor.hospital'])
            ->whereBetween('appointment_date', [$from, $to])
            ->latest('appointment_date')
            ->limit(20)
            ->get();

        $menuItems = $this->menuItems();

        return view('admin.reports', compact(
            'totalVisits', 'totalRevenue', 'newPatients', 'totalAppointments',
            'topDoctors', 'revenueByHospital', 'recentAppointments', 'menuItems'
        ));
    }

    public function revenue(Request $request)
    {
        // Redirect to reports with revenue tab active — handled in same view
        return $this->index($request);
    }

    public function visits(Request $request)
    {
        return $this->index($request);
    }
}
