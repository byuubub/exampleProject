<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id', 'patient_id', 'doctor_id',
        'diagnosis', 'treatment_plan',
        'notes', 'case_status',
    ];

    // case_status: active | resolved | follow_up

    public function appointment()  { return $this->belongsTo(Appointment::class); }
    public function patient()      { return $this->belongsTo(Patient::class); }
    public function doctor()       { return $this->belongsTo(Doctor::class); }
    public function prescriptions(){ return $this->hasMany(Prescription::class); }
}
