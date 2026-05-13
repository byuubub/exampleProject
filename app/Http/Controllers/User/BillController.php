<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;

class BillController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $patient = Patient::where('user_id', $user->id)->first();

        $bills = $patient
            ? Bill::with('appointment.doctor.user')
                ->where('patient_id', $patient->id)
                ->latest()
                ->paginate(15)
            : collect();

        $pendingCount = $patient ? Bill::where('patient_id', $patient->id)->where('status', 'pending')->count() : 0;
        $paidCount    = $patient ? Bill::where('patient_id', $patient->id)->where('status', 'paid')->count() : 0;
        $totalUnpaid  = $patient ? Bill::where('patient_id', $patient->id)->where('status', 'pending')->sum('amount') : 0;

        $menuItems = (new UserDashboardController)->getMenuItems($user->role);

        return view('user.bills', compact('bills', 'pendingCount', 'paidCount', 'totalUnpaid', 'menuItems'));
    }
}
