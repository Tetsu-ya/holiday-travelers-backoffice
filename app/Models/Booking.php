<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_no', 'tour_package_id', 'business_partner_id',
        'customer_name', 'customer_email', 'customer_phone', 'pax',
        'travel_date', 'travel_time', 'total_amount', 'discount_code_id', 'subtotal_amount', 'discount_amount', 'payment_status', 'status',
    ];

    protected $casts = [
        'travel_date' => 'date',
        'total_amount' => 'decimal:2',
        'subtotal_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function tourPackage()
    {
        return $this->belongsTo(TourPackage::class);
    }

    public function businessPartner()
    {
        return $this->belongsTo(BusinessPartner::class);
    }

    public function discountCode()
    {
        return $this->belongsTo(DiscountCode::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(BookingStatusHistory::class)->latest();
    }

    public function partnerCommissions()
    {
        return $this->hasMany(PartnerCommission::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'related');
    }
}
