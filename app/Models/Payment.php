<?php

// app/Models/Payment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $casts = [
        'due_date' => 'date',  // This will automatically cast to Carbon
        'paid_date' => 'date',
    ];

    protected $fillable = [
        'user_id',
        'amount_due',
        'amount_paid',
        'rate_per_liter',
        'due_date',
        'paid_date',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
