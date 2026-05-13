<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Bill;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalRecordController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $patient = Patient::where('user_id', $user->id)->first();

        $records = $patient
            ? MedicalRecord::with(['doctor.user', 'prescriptions'])
                ->where('patient_id', $patient->id)
                ->latest()
                ->paginate(10)
            : collect();

        $menuItems = (new UserDashboardController)->getMenuItems($user->role);

        return view('user.medical-records', compact('records', 'menuItems'));
    }

    /** Doctor: show examination form */
    public function create(Appointment $appointment)
    {
        $user = Auth::user();

        // Verify doctor owns this appointment
        if ($appointment->doctor->user_id !== $user->id) {
            abort(403);
        }

        $menuItems = (new UserDashboardController)->getMenuItems($user->role);

        return view('user.examination', compact('appointment', 'menuItems'));
    }

    /** Doctor: save examination result */
    public function store(Request $request, Appointment $appointment)
    {
        $user = Auth::user();

        if ($appointment->doctor->user_id !== $user->id) {
            abort(403);
        }

        $data = $request->validate([
            'diagnosis'       => 'required|string',
            'treatment_plan'  => 'nullable|string',
            'notes'           => 'nullable|string',
            'case_status'     => 'required|in:active,resolved,follow_up',
            'medicines'       => 'nullable|array',
            'medicines.*.name'        => 'required_with:medicines|string',
            'medicines.*.dosage'      => 'nullable|string',
            'medicines.*.frequency'   => 'nullable|string',
            'medicines.*.duration'    => 'nullable|string',
            'medicines.*.instructions'=> 'nullable|string',
            'bill_amount'     => 'required|numeric|min:0',
        ]);

        $record = MedicalRecord::create([
            'appointment_id' => $appointment->id,
            'patient_id'     => $appointment->patient_id,
            'doctor_id'      => $appointment->doctor_id,
            'diagnosis'      => $data['diagnosis'],
            'treatment_plan' => $data['treatment_plan'] ?? null,
            'notes'          => $data['notes'] ?? null,
            'case_status'    => $data['case_status'],
        ]);

        // Save prescriptions
        if (!empty($data['medicines'])) {
            foreach ($data['medicines'] as $medicine) {
                Prescription::create([
                    'medical_record_id' => $record->id,
                    'patient_id'        => $appointment->patient_id,
                    'doctor_id'         => $appointment->doctor_id,
                    'medicine_name'     => $medicine['name'],
                    'dosage'            => $medicine['dosage'] ?? null,
                    'frequency'         => $medicine['frequency'] ?? null,
                    'duration'          => $medicine['duration'] ?? null,
                    'instructions'      => $medicine['instructions'] ?? null,
                    'status'            => 'active',
                ]);
            }
        }

        // Generate bill
        Bill::create([
            'appointment_id' => $appointment->id,
            'patient_id'     => $appointment->patient_id,
            'amount'         => $data['bill_amount'],
            'description'    => 'Consultation: ' . $data['diagnosis'],
            'status'         => 'pending',
        ]);

        $appointment->update(['status' => 'completed']);

        return redirect()->route('dashboard.today')
            ->with('success', 'Examination saved and bill generated.');
    }
}
