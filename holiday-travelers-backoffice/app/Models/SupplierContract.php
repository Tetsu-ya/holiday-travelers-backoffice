<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SupplierContract extends Model { protected $fillable = ['supplier_id','contract_number','starts_on','ends_on','value','status','notes']; protected $casts = ['starts_on' => 'date', 'ends_on' => 'date', 'value' => 'decimal:2']; public function supplier() { return $this->belongsTo(Supplier::class); } }