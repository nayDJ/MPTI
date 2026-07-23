<?php

namespace App\Models;

use Database\Factories\ProductComponentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductComponent extends Model
{
    /** @use HasFactory<ProductComponentFactory> */
    use HasFactory;
    protected $fillable = [
        'product_id',
        'component_product_id',
        'quantity',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function componentProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'component_product_id');
    }
}
