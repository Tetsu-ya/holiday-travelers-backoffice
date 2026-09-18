<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DiscountCode extends Model { protected $fillable = ['code','description','discount_type','discount_value','usage_limit','used_count','starts_on','ends_on','status']; protected $casts = ['starts_on' => 'date', 'ends_on' => 'date', 'discount_value' => 'decimal:2']; }