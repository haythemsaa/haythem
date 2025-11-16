<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class MedicalCheckup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'checkup_type',
        'checkup_date',
        'next_checkup_date',
        'medical_center',
        'doctor_name',
        'result',
        'restrictions',
        'notes',
        'certificate_file',
    ];

    protected $casts = [
        'checkup_date' => 'date',
        'next_checkup_date' => 'date',
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
        return $query->whereNotNull('next_checkup_date')
                     ->where('next_checkup_date', '<', now());
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('next_checkup_date')
                     ->where('next_checkup_date', '>=', now())
                     ->where('next_checkup_date', '<=', now()->addDays($days));
    }

    public function scopeValid($query)
    {
        return $query->whereNotNull('next_checkup_date')
                     ->where('next_checkup_date', '>', now()->addDays(30));
    }

    public function scopeByType($query, $type)
    {
        return $query->where('checkup_type', $type);
    }

    public function scopeByResult($query, $result)
    {
        return $query->where('result', $result);
    }

    public function scopeWithRestrictions($query)
    {
        return $query->whereNotNull('restrictions')
                     ->where('restrictions', '!=', '');
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('checkup_date', [$startDate, $endDate]);
    }

    /**
     * Accessors
     */
    public function getIsExpiredAttribute(): bool
    {
        if (!$this->next_checkup_date) {
            return false;
        }

        return $this->next_checkup_date->isPast();
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        if (!$this->next_checkup_date || $this->is_expired) {
            return false;
        }

        return $this->next_checkup_date->lte(now()->addDays(30));
    }

    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->next_checkup_date || $this->is_expired) {
            return null;
        }

        return (int) now()->diffInDays($this->next_checkup_date);
    }

    public function getStatusAttribute(): string
    {
        if (!$this->next_checkup_date) {
            return 'unknown';
        }

        if ($this->is_expired) {
            return 'expired';
        }

        if ($this->is_expiring_soon) {
            return 'expiring_soon';
        }

        return 'valid';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'expired' => 'Expiré',
            'expiring_soon' => 'Expire Bientôt',
            'valid' => 'Valide',
            default => 'Inconnu',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'expired' => 'danger',
            'expiring_soon' => 'warning',
            'valid' => 'success',
            default => 'secondary',
        };
    }

    public function getResultLabelAttribute(): string
    {
        return match($this->result) {
            'fit' => 'Apte',
            'fit_with_restrictions' => 'Apte avec Restrictions',
            'temporarily_unfit' => 'Inapte Temporaire',
            'unfit' => 'Inapte',
            default => $this->result,
        };
    }

    public function getResultColorAttribute(): string
    {
        return match($this->result) {
            'fit' => 'success',
            'fit_with_restrictions' => 'warning',
            'temporarily_unfit' => 'warning',
            'unfit' => 'danger',
            default => 'secondary',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->checkup_type) {
            'initial' => 'Visite Initiale',
            'periodic' => 'Visite Périodique',
            'renewal' => 'Visite de Reprise',
            'pre_assignment' => 'Visite Pré-Affectation',
            'spontaneous' => 'Visite à la Demande',
            default => $this->checkup_type,
        };
    }
}
