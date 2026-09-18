<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourSchedule extends Model
{
    protected $fillable = ['tour_package_id', 'schedule_date', 'starts_at', 'ends_at', 'location', 'capacity', 'status', 'notes'];
    protected $casts = ['schedule_date' => 'date'];

    public function tourPackage() { return $this->belongsTo(TourPackage::class); }
    public function allocations() { return $this->hasMany(ResourceAllocation::class); }
    public function assignments() { return $this->hasMany(StaffAssignment::class); }
}