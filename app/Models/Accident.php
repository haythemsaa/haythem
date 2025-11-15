<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Accident extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'vehicle_id',
        'accident_date',
        'accident_time',
        'location',
        'description',
        'severity',
        'injured_count',
        'fatalities_count',
        'material_damage',
        'third_parties',
        'police_report_number',
        'insurance_claim_number',
        'estimated_cost',
        'status',
    ];

    protected $casts = [
        'accident_date' => 'date',
        'accident_time' => 'datetime:H:i',
        'injured_count' => 'integer',
        'fatalities_count' => 'integer',
        'estimated_cost' => 'decimal:2',
    ];

    // Relations
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function accidentDocuments(): HasMany
    {
        return $this->hasMany(AccidentDocument::class);
    }

    // Accessors
    public function getSeverityLabelAttribute(): string
    {
        return match($this->severity) {
            'mineure' => 'Mineure',
            'serieuse' => 'Sérieuse',
            'grave' => 'Grave',
            'critique' => 'Critique',
            default => ucfirst($this->severity),
        };
    }

    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'mineure' => 'info',
            'serieuse' => 'warning',
            'grave' => 'danger',
            'critique' => 'dark',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'en_cours' => 'En Cours',
            'cloture' => 'Clôturé',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'en_cours' => 'warning',
            'cloture' => 'success',
            default => 'secondary',
        };
    }

    public function getIsCriticalAttribute(): bool
    {
        return in_array($this->severity, ['grave', 'critique']) ||
               $this->fatalities_count > 0 ||
               $this->injured_count > 0;
    }

    public function getHasInsuranceClaimAttribute(): bool
    {
        return !empty($this->insurance_claim_number);
    }

    public function getHasPoliceReportAttribute(): bool
    {
        return !empty($this->police_report_number);
    }

    // Scopes
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'en_cours');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'cloture');
    }

    public function scopeCritical($query)
    {
        return $query->where(function ($q) {
            $q->whereIn('severity', ['grave', 'critique'])
              ->orWhere('fatalities_count', '>', 0)
              ->orWhere('injured_count', '>', 0);
        });
    }

    public function scopeWithInjuries($query)
    {
        return $query->where('injured_count', '>', 0);
    }

    public function scopeWithFatalities($query)
    {
        return $query->where('fatalities_count', '>', 0);
    }

    public function scopeByVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('accident_date', '>=', now()->subDays($days));
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('accident_date', [$startDate, $endDate]);
    }
}
