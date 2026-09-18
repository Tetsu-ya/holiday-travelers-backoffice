<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StaffSchedule extends Model { protected $fillable = ['user_id','schedule_date','starts_at','ends_at','location','notes']; protected $casts = ['schedule_date' => 'date']; public function user() { return $this->belongsTo(User::class); } }