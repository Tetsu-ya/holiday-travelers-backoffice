<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SupplierPerformance extends Model { protected $fillable = ['supplier_id','period','score','bookings_completed','notes']; protected $casts = ['score' => 'decimal:2']; public function supplier() { return $this->belongsTo(Supplier::class); } }