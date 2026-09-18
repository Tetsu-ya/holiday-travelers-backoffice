<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessPartner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'type', 'contact_person', 'email', 'phone',
        'region', 'commission_rate', 'status', 'performance_score',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
