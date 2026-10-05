<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $casts = [
        'agreement_valid_until' => 'date',
    ];

    protected $fillable = [
        'name', 'category', 'contact_person', 'email', 'phone',
        'location', 'base_rate', 'reliability_rating', 'status',
        'agreement_status', 'agreement_valid_until', 'perks_inclusions',
    ];

    public function tourPackages()
    {
        return $this->hasMany(TourPackage::class, 'primary_supplier_id');
    }
}
