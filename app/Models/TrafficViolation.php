<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrafficViolation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'vehicle_id',
        'violation_date',
        'violation_type',
        'location',
        'fine_amount',
        'points_deducted',
        'additional_costs',
        'consequences',
        'attachment',
    ];

    protected $casts = [
        'violation_date' => 'date',
        'fine_amount' => 'decimal:2',
        'additional_costs' => 'decimal:2',
        'points_deducted' => 'integer',
    ];

    // Relations
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    // Accessors
    public function getViolationTypeNameAttribute(): string
    {
        return match($this->violation_type) {
            'speeding' => 'Excès de Vitesse',
            'red_light' => 'Feu Rouge',
            'parking' => 'Stationnement Interdit',
            'phone' => 'Téléphone au Volant',
            'seatbelt' => 'Ceinture de Sécurité',
            'drunk_driving' => 'Conduite en État d\'Ivresse',
            'dangerous_driving' => 'Conduite Dangereuse',
            'no_insurance' => 'Défaut d\'Assurance',
            'no_license' => 'Défaut de Permis',
            'overload' => 'Surcharge',
            'technical_fault' => 'Défaut Technique',
            'other' => 'Autre',
            default => ucfirst($this->violation_type),
        };
    }

    public function getTotalCostAttribute(): float
    {
        return ($this->fine_amount ?? 0) + ($this->additional_costs ?? 0);
    }

    public function getHasConsequencesAttribute(): bool
    {
        return !empty($this->consequences);
    }

    public function getHasAttachmentAttribute(): bool
    {
        return !empty($this->attachment);
    }

    public function getSeverityLevelAttribute(): string
    {
        // Determine severity based on points deducted or fine amount
        if ($this->points_deducted >= 6 || $this->fine_amount >= 3000) {
            return 'critical';
        } elseif ($this->points_deducted >= 3 || $this->fine_amount >= 1000) {
            return 'high';
        } elseif ($this->points_deducted >= 1 || $this->fine_amount >= 500) {
            return 'medium';
        }
        return 'low';
    }

    public function getSeverityColorAttribute(): string
    {
        return match($this->severity_level) {
            'critical' => 'danger',
            'high' => 'warning',
            'medium' => 'info',
            'low' => 'secondary',
            default => 'secondary',
        };
    }

    // Scopes
    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('violation_type', $type);
    }

    public function scopeWithPoints($query)
    {
        return $query->where('points_deducted', '>', 0);
    }

    public function scopeHighSeverity($query)
    {
        return $query->where(function ($q) {
            $q->where('points_deducted', '>=', 3)
              ->orWhere('fine_amount', '>=', 1000);
        });
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('violation_date', [$startDate, $endDate]);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('violation_date', '>=', now()->subDays($days));
    }
}
