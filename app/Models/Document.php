<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'related_type',
        'related_id',
        'document_type',
        'file_name',
        'file_path',
        'expires_on',
        'status',
        'uploaded_by',
    ];

    protected $casts = ['expires_on' => 'date'];

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function related()
    {
        return $this->morphTo();
    }
}
