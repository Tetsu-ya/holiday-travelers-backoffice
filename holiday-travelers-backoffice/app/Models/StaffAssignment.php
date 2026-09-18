<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffAssignment extends Model
{
    protected $fillable = ['tour_schedule_id', 'user_id', 'assignment_role', 'status', 'notes'];
    public function tourSchedule() { return $this->belongsTo(TourSchedule::class); }
    public function user() { return $this->belongsTo(User::class); }
}