<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'hospital_id', 'specialization_id',
        'license_number', 'consultation_fee', 'experience_years',
        'education', 'bio', 'status',
    ];

    public function user()         { return $this->belongsTo(User::class); }
    public function hospital()     { return $this->belongsTo(Hospital::class); }
    public function specialization(){ return $this->belongsTo(Specialization::class); }
    public function schedules()    { return $this->hasMany(Schedule::class); }
    public function appointments() { return $this->hasMany(Appointment::class); }
}
