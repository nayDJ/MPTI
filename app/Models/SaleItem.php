<?php

namespace App\Models;

use Database\Factories\SaleItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    /** @use HasFactory<SaleItemFactory> */
    use HasFactory;

    protected $fillable = [
        'sales_id',
        'product_id',
        'quantity',
        'subtotal'
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'sales_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}