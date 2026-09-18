<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'type', 'destination', 'description', 'price',
        'duration_days', 'slots', 'primary_supplier_id', 'status',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'primary_supplier_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
        public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
