<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StaffCommission extends Model { protected $fillable = ['user_id','period','basis_amount','rate','amount','status']; protected $casts = ['basis_amount' => 'decimal:2', 'rate' => 'decimal:2', 'amount' => 'decimal:2']; public function user() { return $this->belongsTo(User::class); } }