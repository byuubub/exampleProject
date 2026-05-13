<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientAppointmentController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $patient = Patient::where('user_id', $user->id)->first();

        $appointments = $patient
            ? Appointment::with(['doctor.user', 'doctor.specialization'])
                ->where('patient_id', $patient->id)
                ->latest('appointment_date')
                ->paginate(10)
            : collect();

        $doctors = Doctor::with(['user', 'specialization'])
            ->where('status', 'active')
            ->get();

        $menuItems = (new UserDashboardController)->getMenuItems($user->role);

        return view('user.appointments', compact('appointments', 'doctors', 'menuItems'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'doctor_id'        => 'required|exists:doctors,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'complaint'        => 'required|string|min:5',
        ]);

        $user    = Auth::user();
        $patient = Patient::firstOrCreate(
            ['user_id' => $user->id],
            []
        );

        Appointment::create([
            'patient_id'       => $patient->id,
            'doctor_id'        => $data['doctor_id'],
            'appointment_date' => $data['appointment_date'],
            'appointment_time' => $data['appointment_time'],
            'complaint'        => $data['complaint'],
            'status'           => 'scheduled',
        ]);

        return redirect()->route('dashboard.appointments')
            ->with('success', 'Appointment booked successfully!');
    }

    public function destroy(Appointment $appointment)
    {
        $user    = Auth::user();
        $patient = Patient::where('user_id', $user->id)->first();

        if (!$patient || $appointment->patient_id !== $patient->id) {
            abort(403);
        }

        if (!in_array($appointment->status, ['scheduled', 'confirmed'])) {
            return back()->with('error', 'This appointment cannot be cancelled.');
        }

        $appointment->update(['status' => 'cancelled']);

        return back()->with('success', 'Appointment cancelled.');
    }
}
