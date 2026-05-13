<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;

class PatientHistoryController extends Controller
{
    public function show($patientId)
    {
        $user   = Auth::user();
        $doctor = Doctor::where('user_id', $user->id)->firstOrFail();

        $patient = Patient::with('user')->findOrFail($patientId);

        // Verify doctor has seen this patient
        $hasAccess = Appointment::where('doctor_id', $doctor->id)
            ->where('patient_id', $patientId)
            ->exists();

        if (!$hasAccess) abort(403);

        $records = MedicalRecord::with(['prescriptions', 'doctor.user'])
            ->where('patient_id', $patientId)
            ->latest()
            ->get();

        $appointments = Appointment::where('patient_id', $patientId)
            ->latest('appointment_date')
            ->get();

        $menuItems = (new UserDashboardController)->getMenuItems($user->role);

        return view('user.patient-history', compact('patient', 'records', 'appointments', 'menuItems'));
    }
}
