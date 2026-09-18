<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SupplierAvailability extends Model { protected $table = 'supplier_availability'; protected $fillable = ['supplier_id','available_on','capacity','status','notes']; protected $casts = ['available_on' => 'date']; public function supplier() { return $this->belongsTo(Supplier::class); } }