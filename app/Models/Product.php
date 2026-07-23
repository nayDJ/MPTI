<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'category',
        'stock',
        'price',
        'low_stock_threshold',
        'low_stock_alert_enabled',
        'is_active',
        'track_stock',
    ];

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function components(): HasMany
    {
        return $this->hasMany(ProductComponent::class);
    }
}