<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonalProtectiveEquipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'personal_protective_equipment';

    protected $fillable = [
        'employee_id',
        'equipment_type',
        'equipment_name',
        'brand',
        'size',
        'issue_date',
        'expiry_date',
        'replacement_frequency_months',
        'condition',
        'status',
        'serial_number',
        'cost',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'replacement_frequency_months' => 'integer',
        'cost' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Scopes
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
                     ->where('expiry_date', '<', now())
                     ->where('status', 'in_use');
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
                     ->where('expiry_date', '>=', now())
                     ->where('expiry_date', '<=', now()->addDays($days))
                     ->where('status', 'in_use');
    }

    public function scopeInUse($query)
    {
        return $query->where('status', 'in_use');
    }

    public function scopeRetired($query)
    {
        return $query->where('status', 'retired');
    }

    public function scopeLost($query)
    {
        return $query->where('status', 'lost');
    }

    public function scopeDamaged($query)
    {
        return $query->where('condition', 'damaged');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('equipment_type', $type);
    }

    public function scopeByCondition($query, $condition)
    {
        return $query->where('condition', $condition);
    }

    /**
     * Accessors
     */
    public function getIsExpiredAttribute(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        return $this->expiry_date->isPast();
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        if (!$this->expiry_date || $this->is_expired) {
            return false;
        }

        return $this->expiry_date->lte(now()->addDays(30));
    }

    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->expiry_date || $this->is_expired) {
            return null;
        }

        return (int) now()->diffInDays($this->expiry_date);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'in_use' => 'En Service',
            'retired' => 'Retiré',
            'lost' => 'Perdu',
            'replaced' => 'Remplacé',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'in_use' => 'success',
            'retired' => 'secondary',
            'lost' => 'danger',
            'replaced' => 'info',
            default => 'secondary',
        };
    }

    public function getConditionLabelAttribute(): string
    {
        return match($this->condition) {
            'new' => 'Neuf',
            'good' => 'Bon',
            'fair' => 'Acceptable',
            'worn' => 'Usé',
            'damaged' => 'Endommagé',
            default => $this->condition,
        };
    }

    public function getConditionColorAttribute(): string
    {
        return match($this->condition) {
            'new' => 'success',
            'good' => 'success',
            'fair' => 'warning',
            'worn' => 'warning',
            'damaged' => 'danger',
            default => 'secondary',
        };
    }

    public function getExpiryStatusAttribute(): string
    {
        if (!$this->expiry_date) {
            return 'no_expiry';
        }

        if ($this->is_expired) {
            return 'expired';
        }

        if ($this->is_expiring_soon) {
            return 'expiring_soon';
        }

        return 'valid';
    }

    public function getExpiryStatusColorAttribute(): string
    {
        return match($this->expiry_status) {
            'expired' => 'danger',
            'expiring_soon' => 'warning',
            'valid' => 'success',
            default => 'secondary',
        };
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($equipment) {
            // Calculate expiry date based on replacement frequency
            if (!$equipment->expiry_date && $equipment->replacement_frequency_months && $equipment->issue_date) {
                $equipment->expiry_date = $equipment->issue_date->copy()->addMonths($equipment->replacement_frequency_months);
            }
        });
    }
}
