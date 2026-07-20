<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Sale extends Model
{
    protected $fillable = [
        'customer_id',
        'total_price',
        'sales_date',
        'payment_status',
        'paid_amount'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class, 'sales_id');
    }

    // ponytail: centralize payment revenue logic (was repeated 5x across controllers)
    public function scopeCollectableRevenue($query)
    {
        return $query->selectRaw(
            "SUM(CASE WHEN payment_status = 'lunas' THEN total_price WHEN payment_status = 'cicil' THEN paid_amount ELSE 0 END) as total"
        );
    }
}
