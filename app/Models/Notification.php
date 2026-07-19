<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'type',
        'title',
        'message',
        'action_type',
        'notifiable_id',
        'notifiable_type',
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

    public function notifiable()
    {
        return $this->morphTo();
    }
}
