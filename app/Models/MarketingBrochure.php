<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketingBrochure extends Model
{
    protected $fillable = [
        'title',
        'description',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'uploaded_by',
    ];

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(MarketingCampaign::class, 'marketing_brochure_id');
    }
}
