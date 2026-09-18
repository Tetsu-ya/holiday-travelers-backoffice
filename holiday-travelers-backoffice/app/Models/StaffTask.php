<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StaffTask extends Model { protected $fillable = ['assigned_to','title','description','due_date','priority','status']; protected $casts = ['due_date' => 'date']; public function assignee() { return $this->belongsTo(User::class, 'assigned_to'); } }