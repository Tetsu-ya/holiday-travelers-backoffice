<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StaffPerformance extends Model { protected $fillable = ['user_id','period','score','bookings_completed','revenue_generated','notes']; protected $casts = ['score' => 'decimal:2', 'revenue_generated' => 'decimal:2']; public function user() { return $this->belongsTo(User::class); } }