<?php

namespace App\Models;

use Database\Factories\NotificationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    /** @use HasFactory<NotificationFactory> */
    use HasFactory;
    // ponytail: centralize notification types and actions (was hardcoded in 14 places)
    const TYPE_SUCCESS = 'success';
    const TYPE_ERROR = 'error';
    const TYPE_INFO = 'info';

    const ACTION_SALE_CREATE = 'sale.create';
    const ACTION_SALE_UPDATE = 'sale.update';
    const ACTION_SALE_DELETE = 'sale.delete';

    const ACTION_PRODUCT_CREATE = 'product.create';
    const ACTION_PRODUCT_UPDATE = 'product.update';
    const ACTION_PRODUCT_DELETE = 'product.delete';
    const ACTION_PRODUCT_TOGGLE = 'product.toggle';

    const ACTION_CUSTOMER_CREATE = 'customer.create';
    const ACTION_CUSTOMER_UPDATE = 'customer.update';
    const ACTION_CUSTOMER_DELETE = 'customer.delete';
    const ACTION_CUSTOMER_TOGGLE = 'customer.toggle';

    const ACTION_EXPENSE_CREATE = 'expense.create';
    const ACTION_EXPENSE_UPDATE = 'expense.update';
    const ACTION_EXPENSE_DELETE = 'expense.delete';

    protected $fillable = [
        'type',
        'title',
        'message',
        'action_type',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    public function scopeUnread($q)
    {
        return $q->where('is_read', false);
    }

    public function scopeByType($q, $type)
    {
        return $type ? $q->where('action_type', 'like', "$type.%") : $q;
    }

    public function scopeOlderThanDays($q, $days)
    {
        return $q->where('created_at', '<', now()->subDays($days));
    }
}
