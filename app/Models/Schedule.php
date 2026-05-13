<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id', 'day_of_week',
        'start_time', 'end_time',
        'quota', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function doctor()       { return $this->belongsTo(Doctor::class); }
    public function appointments() { return $this->hasMany(Appointment::class); }
}
