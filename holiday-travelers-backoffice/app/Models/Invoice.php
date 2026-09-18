<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'invoice_number',
        'subtotal',
        'tax',
        'total',
        'status',
        'issued_at',
        'due_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'issued_at' => 'date',
        'due_at' => 'date',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
