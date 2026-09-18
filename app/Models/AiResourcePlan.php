<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiResourcePlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_type', 'subject', 'input_snapshot', 'recommendation',
        'summary', 'confidence_score', 'status',
    ];

    protected $casts = [
        'input_snapshot' => 'array',
        'recommendation' => 'array',
    ];
}
