<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'channel', 'tour_package_id', 'budget', 'actual_spend',
        'leads_generated', 'conversions', 'start_date', 'end_date', 'status',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'actual_spend' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function roi(): float
    {
        if ($this->actual_spend <= 0) return 0;
        // simplified ROI placeholder; real value calc happens in AIResourcePlanningService
        return round((($this->conversions * 100) - $this->actual_spend) / $this->actual_spend * 100, 2);
    }
}
