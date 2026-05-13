<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;

class DoctorScheduleController extends Controller
{
    public function index()
    {
        $user   = Auth::user();
        $doctor = Doctor::where('user_id', $user->id)->firstOrFail();

        $schedules = Schedule::where('doctor_id', $doctor->id)->get();

        // Upcoming appointments for the week
        $weekAppointments = Appointment::with('patient.user')
            ->where('doctor_id', $doctor->id)
            ->whereBetween('appointment_date', [today(), today()->addDays(7)])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get()
            ->groupBy(fn($a) => $a->appointment_date->format('Y-m-d'));

        $menuItems = (new UserDashboardController)->getMenuItems($user->role);

        return view('user.schedule', compact('schedules', 'weekAppointments', 'menuItems'));
    }
}
