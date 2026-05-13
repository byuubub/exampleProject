<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Support\Facades\Auth;

class DoctorAppointmentController extends Controller
{
    public function index()
    {
        $user   = Auth::user();
        $doctor = Doctor::where('user_id', $user->id)->firstOrFail();

        $appointments = Appointment::with(['patient.user'])
            ->where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', today())
            ->orderBy('appointment_time')
            ->paginate(20);

        $menuItems = (new UserDashboardController)->getMenuItems($user->role);

        return view('user.today-appointments', compact('appointments', 'menuItems'));
    }
}
