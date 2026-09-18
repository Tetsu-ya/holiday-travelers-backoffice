<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SupplierRate extends Model { protected $fillable = ['supplier_id','service','rate','unit','effective_from','effective_until']; protected $casts = ['effective_from' => 'date', 'effective_until' => 'date', 'rate' => 'decimal:2']; public function supplier() { return $this->belongsTo(Supplier::class); } }