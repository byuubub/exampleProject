<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hospital extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city',
        'phone',
        'email',
        'image_url',
        'description',
        'status',
    ];

    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

}
