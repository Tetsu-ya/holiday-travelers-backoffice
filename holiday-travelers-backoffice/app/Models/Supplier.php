<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'category', 'contact_person', 'email', 'phone',
        'location', 'base_rate', 'reliability_rating', 'status',
    ];

    public function tourPackages()
    {
        return $this->hasMany(TourPackage::class, 'primary_supplier_id');
    }
}
