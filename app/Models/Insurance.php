<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Insurance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'insurance_company',
        'policy_number',
        'insurance_type',
        'coverage_type',
        'start_date',
        'end_date',
        'premium_amount',
        'deductible',
        'status',
        'notes',
        'policy_document',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'premium_amount' => 'decimal:2',
        'deductible' => 'decimal:2',
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
            'active' => 'Active',
            'expired' => 'Expirée',
            'cancelled' => 'Annulée',
            'suspended' => 'Suspendue',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'expired' => 'danger',
            'cancelled' => 'secondary',
            'suspended' => 'warning',
            default => 'secondary',
        };
    }
}
