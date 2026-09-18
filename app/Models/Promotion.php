<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Promotion extends Model { protected $fillable = ['name','description','discount_type','discount_value','starts_on','ends_on','status']; protected $casts = ['starts_on' => 'date', 'ends_on' => 'date', 'discount_value' => 'decimal:2']; }