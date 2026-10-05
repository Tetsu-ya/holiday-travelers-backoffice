<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingStaffAssignment extends Model
{
    protected $fillable = ['booking_id', 'user_id', 'assignment_role', 'status', 'notes'];

    public function booking() { return $this->belongsTo(Booking::class); }
    public function user() { return $this->belongsTo(User::class); }
}
