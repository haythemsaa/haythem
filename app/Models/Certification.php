<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certification extends Model
{
    protected $fillable = [
        'employee_id',
        'name',
        'certification_number',
        'issue_date',
        'expiry_date',
        'issuing_organization',
        'attachment',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    // Relations
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
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
        return $this->expiry_date->diffInDays(now()) <= 30; // 1 month
    }

    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->expiry_date || $this->is_expired) {
            return null;
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
            return 'Expire Bientôt';
        }
        return 'Valide';
    }

    public function getCertificationTypeAttribute(): string
    {
        // Common certifications in Morocco/France
        $types = [
            'CACES' => 'CACES (Certificat d\'Aptitude à la Conduite En Sécurité)',
            'ADR' => 'ADR (Transport de Matières Dangereuses)',
            'FIMO' => 'FIMO (Formation Initiale Minimale Obligatoire)',
            'FCO' => 'FCO (Formation Continue Obligatoire)',
            'AIPR' => 'AIPR (Autorisation d\'Intervention à Proximité des Réseaux)',
            'SST' => 'SST (Sauveteur Secouriste du Travail)',
            'CACES R489' => 'CACES R489 (Chariots automoteurs)',
            'CACES R482' => 'CACES R482 (Engins de chantier)',
        ];

        foreach ($types as $key => $value) {
            if (str_contains(strtoupper($this->name), $key)) {
                return $value;
            }
        }

        return $this->name;
    }

    // Scopes
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
                    ->where('expiry_date', '<', now());
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
                    ->where('expiry_date', '>', now())
                    ->where('expiry_date', '<=', now()->addDays($days));
    }

    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expiry_date')
              ->orWhere('expiry_date', '>', now());
        });
    }

    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('name', 'like', "%{$type}%");
    }
}
