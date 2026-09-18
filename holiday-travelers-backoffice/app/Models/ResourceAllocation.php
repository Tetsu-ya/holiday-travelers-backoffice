<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceAllocation extends Model
{
    protected $fillable = ['tour_schedule_id', 'resource_type', 'resource_name', 'quantity', 'status', 'notes'];
    public function tourSchedule() { return $this->belongsTo(TourSchedule::class); }
}