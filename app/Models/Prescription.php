<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_record_id', 'patient_id', 'doctor_id',
        'medicine_name', 'dosage', 'frequency',
        'duration', 'instructions', 'status',
    ];

    // status: active | completed | cancelled

    public function medicalRecord() { return $this->belongsTo(MedicalRecord::class); }
    public function patient()       { return $this->belongsTo(Patient::class); }
    public function doctor()        { return $this->belongsTo(Doctor::class); }
}
