<?php

namespace App\Models;

use Database\Factories\ExpenseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    /** @use HasFactory<ExpenseFactory> */
    use HasFactory;
    protected $fillable = [
        'description',
        'amount',
        'category',
        'expense_date',
    ];
}
