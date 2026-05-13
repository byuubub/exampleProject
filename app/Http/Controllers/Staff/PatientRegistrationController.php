<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Patient;
use App\Models\Queue;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PatientRegistrationController extends Controller
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

    public function index()
    {
        $hospitalId = Auth::user()->hospital_id;

        $doctors   = Doctor::with(['user', 'specialization'])
            ->where('status', 'active')
            ->when($hospitalId, fn($q) => $q->where('hospital_id', $hospitalId))
            ->get();

        $schedules = Schedule::with('doctor.user')
            ->where('is_active', true)
            ->whereHas('doctor', fn($q) => $q->where('status', 'active')
                ->when($hospitalId, fn($d) => $d->where('hospital_id', $hospitalId)))
            ->get();

        $menuItems = $this->menuItems();

        return view('staff.registration', compact('doctors', 'schedules', 'menuItems'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name'        => 'required|string|max:255',
            'email'            => 'required|email',
            'phone'            => 'nullable|string|max:20',
            'date_of_birth'    => 'nullable|date',
            'gender'           => 'nullable|in:male,female',
            'blood_type'       => 'nullable|in:A,B,AB,O',
            'address'          => 'nullable|string',
            'insurance_number' => 'nullable|string|max:100',
            'doctor_id'        => 'required|exists:doctors,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'complaint'        => 'required|string',
        ]);

        DB::transaction(function () use ($data) {
            // Find or create user account
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'full_name' => $data['full_name'],
                    'phone'     => $data['phone'] ?? null,
                    'role'      => 'patient',
                    'password'  => Hash::make('password123'),
                ]
            );

            // Find or create patient record
            $patient = Patient::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'date_of_birth'    => $data['date_of_birth'] ?? null,
                    'gender'           => $data['gender'] ?? null,
                    'blood_type'       => $data['blood_type'] ?? null,
                    'address'          => $data['address'] ?? null,
                    'insurance_number' => $data['insurance_number'] ?? null,
                ]
            );

            // Get doctor's schedule for this day
            $date      = $data['appointment_date'];
            $dayName   = \Carbon\Carbon::parse($date)->format('l');
            $schedule  = Schedule::where('doctor_id', $data['doctor_id'])
                ->where('day_of_week', $dayName)
                ->where('is_active', true)
                ->first();

            // Create appointment
            $appointment = Appointment::create([
                'patient_id'       => $patient->id,
                'doctor_id'        => $data['doctor_id'],
                'schedule_id'      => $schedule?->id,
                'appointment_date' => $date,
                'appointment_time' => $schedule?->start_time ?? '08:00',
                'complaint'        => $data['complaint'],
                'status'           => 'confirmed',
            ]);

            // Generate queue number
            $lastQueue = Queue::whereDate('queue_date', $date)
                ->where('doctor_id', $data['doctor_id'])
                ->max('queue_number');

            Queue::create([
                'appointment_id' => $appointment->id,
                'patient_id'     => $patient->id,
                'doctor_id'      => $data['doctor_id'],
                'queue_number'   => ($lastQueue ?? 0) + 1,
                'queue_date'     => $date,
                'status'         => 'waiting',
            ]);
        });

        return redirect()->route('staff.queue')->with('success', 'Patient registered and queue number assigned!');
    }
}
