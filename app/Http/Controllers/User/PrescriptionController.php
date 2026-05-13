<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Support\Facades\Auth;

class PrescriptionController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'doctor') {
            $doctor = Doctor::where('user_id', $user->id)->firstOrFail();
            $prescriptions = Prescription::with(['patient.user', 'medicalRecord'])
                ->where('doctor_id', $doctor->id)
                ->latest()
                ->paginate(15);
        } else {
            $patient = Patient::where('user_id', $user->id)->firstOrFail();
            $prescriptions = Prescription::with(['doctor.user', 'medicalRecord'])
                ->where('patient_id', $patient->id)
                ->latest()
                ->paginate(15);
        }

        $menuItems = (new UserDashboardController)->getMenuItems($user->role);

        return view('user.prescriptions', compact('prescriptions', 'menuItems'));
    }
}
