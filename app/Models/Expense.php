<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = ['description', 'category', 'amount', 'expense_date', 'status', 'notes'];
    protected $casts = ['amount' => 'decimal:2', 'expense_date' => 'date'];
}