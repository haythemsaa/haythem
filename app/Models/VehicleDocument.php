<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class VehicleDocument extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'document_type',
        'document_number',
        'issue_date',
        'expiry_date',
        'issuing_authority',
        'file_path',
        'file_name',
        'notes',
        'is_valid',
        'reminder_days',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'is_valid' => 'boolean',
        'reminder_days' => 'integer',
    ];

    // Relations
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    // Accessors
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

        $reminderDays = $this->reminder_days ?? 30;
        return $this->expiry_date->diffInDays(now()) <= $reminderDays;
    }

    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->expiry_date) {
            return null;
        }

        if ($this->is_expired) {
            return -1 * $this->expiry_date->diffInDays(now());
        }

        return $this->expiry_date->diffInDays(now());
    }

    public function getStatusColorAttribute(): string
    {
        if ($this->is_expired) {
            return 'danger';
        }

        if ($this->is_expiring_soon) {
            return 'warning';
        }

        return 'success';
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->is_expired) {
            return 'Expiré';
        }

        if ($this->is_expiring_soon) {
            return 'Expire bientôt';
        }

        return 'Valide';
    }

    public function getDocumentTypeNameAttribute(): string
    {
        return match($this->document_type) {
            'insurance' => 'Assurance',
            'registration' => 'Carte Grise',
            'technical_control' => 'Contrôle Technique',
            'authorization' => 'Autorisation de Circulation',
            'pollution_control' => 'Contrôle Anti-Pollution',
            'contract' => 'Contrat',
            'lease' => 'Leasing',
            'other' => 'Autre',
            default => ucfirst($this->document_type),
        };
    }

    // Scopes
    public function scopeByVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now());
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
            ->where('expiry_date', '>=', now())
            ->where('expiry_date', '<=', now()->addDays($days));
    }

    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expiry_date')
              ->orWhere('expiry_date', '>=', now());
        });
    }

    public function scopeNeedingRenewal($query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
            ->where(function ($q) use ($days) {
                $q->where('expiry_date', '<', now())
                  ->orWhere('expiry_date', '<=', now()->addDays($days));
            });
    }
}
