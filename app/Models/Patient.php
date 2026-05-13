<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'medical_record_number',
        'date_of_birth', 'gender', 'blood_type',
        'emergency_contact_name', 'emergency_contact_phone',
        'insurance_number', 'insurance_provider', 'address',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function user()         { return $this->belongsTo(User::class); }
    public function appointments() { return $this->hasMany(Appointment::class); }
    public function medicalRecords(){ return $this->hasMany(MedicalRecord::class); }
    public function bills()        { return $this->hasMany(Bill::class); }
    public function queues()       { return $this->hasMany(Queue::class); }

    // Auto-generate MRN on create
    protected static function booted(): void
    {
        static::creating(function (Patient $patient) {
            if (empty($patient->medical_record_number)) {
                $patient->medical_record_number = 'MRN-' . strtoupper(uniqid());
            }
        });
    }

    // Helper
    public function getFullNameAttribute(): string
    {
        return $this->user->full_name ?? '';
    }
}
