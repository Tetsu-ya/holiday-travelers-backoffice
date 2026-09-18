<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class VisaApplication extends Model { protected $fillable = ['booking_id','customer_name','country','visa_type','travel_date','submitted_on','status','notes']; protected $casts = ['travel_date' => 'date', 'submitted_on' => 'date']; public function booking() { return $this->belongsTo(Booking::class); } }