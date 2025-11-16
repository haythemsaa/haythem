<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'contract_type',
        'contract_number',
        'supplier_name',
        'start_date',
        'end_date',
        'monthly_cost',
        'total_cost',
        'payment_frequency',
        'auto_renewal',
        'status',
        'notes',
        'contract_document',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'monthly_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'auto_renewal' => 'boolean',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now());
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('end_date', '>=', now())
                     ->where('end_date', '<=', now()->addDays($days));
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->end_date && $this->end_date->isPast();
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        if (!$this->end_date || $this->is_expired) return false;
        return $this->end_date->lte(now()->addDays(30));
    }

    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->end_date || $this->is_expired) return null;
        return (int) now()->diffInDays($this->end_date);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active' => 'Actif',
            'expired' => 'Expiré',
            'terminated' => 'Résilié',
            'pending' => 'En Attente',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'expired' => 'danger',
            'terminated' => 'secondary',
            'pending' => 'warning',
            default => 'secondary',
        };
    }
}
