<?php
// ============================================================
// App\Models\Appointment
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'doctor_id', 'schedule_id',
        'appointment_date', 'appointment_time',
        'complaint', 'status', 'notes',
    ];

    protected $casts = [
        'appointment_date' => 'date',
    ];

    // status: scheduled | confirmed | in_progress | completed | cancelled

    public function patient()  { return $this->belongsTo(Patient::class); }
    public function doctor()   { return $this->belongsTo(Doctor::class); }
    public function schedule() { return $this->belongsTo(Schedule::class); }
    public function bill()     { return $this->hasOne(Bill::class); }
    public function medicalRecord() { return $this->hasOne(MedicalRecord::class); }
    public function queue()    { return $this->hasOne(Queue::class); }
}
