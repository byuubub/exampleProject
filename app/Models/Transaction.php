<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id', 'amount',
        'payment_method', 'payment_proof',
        'processed_by', 'status', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // payment_method: cash | bank_transfer | qris | insurance | va | ewallet
    // status: pending | completed | failed

    public function bill()        { return $this->belongsTo(Bill::class); }
    public function processedBy() { return $this->belongsTo(User::class, 'processed_by'); }
}
