<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'type', 'notifiable_type', 'notifiable_id',
        'title', 'message', 'data', 'read_at', 'sent_at', 'status'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    // Polymorphic relationship
    public function notifiable()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Methods
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    public function markAsSent()
    {
        $this->update([
            'sent_at' => now(),
            'status' => 'sent'
        ]);
    }

    public function markAsFailed()
    {
        $this->update(['status' => 'failed']);
    }
}
