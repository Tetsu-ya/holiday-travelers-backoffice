<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class VisaRequirement extends Model { protected $fillable = ['country','visa_type','document_name','description','required']; protected $casts = ['required' => 'boolean']; }