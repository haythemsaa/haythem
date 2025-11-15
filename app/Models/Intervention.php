<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Intervention extends Model
{
    protected $fillable = [
        'vehicle_id',
        'employee_id',
        'intervention_category_id',
        'title',
        'description',
        'type',
        'urgency',
        'severity',
        'request_date',
        'mileage_at_request',
        'status',
    ];

    protected $casts = [
        'request_date' => 'date',
    ];

    // Relations
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(InterventionCategory::class, 'intervention_category_id');
    }

    public function diagnostic(): HasOne
    {
        return $this->hasOne(Diagnostic::class);
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function stockOutputs(): HasMany
    {
        return $this->hasMany(StockOutput::class);
    }

    // Accessors
    public function getIsUrgentAttribute(): bool
    {
        return in_array($this->urgency, ['tres_urgent', 'urgent']);
    }

    public function getIsCriticalAttribute(): bool
    {
        return $this->severity === 'critique';
    }

    public function getIsClosedAttribute(): bool
    {
        return $this->status === 'cloture';
    }

    public function getTotalCostAttribute(): float
    {
        return $this->workOrders->sum('total_cost');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'en_attente');
    }

    public function scopeInProgress($query)
    {
        return $query->whereIn('status', ['diagnostique', 'en_reparation']);
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'cloture');
    }

    public function scopeUrgent($query)
    {
        return $query->whereIn('urgency', ['tres_urgent', 'urgent']);
    }

    public function scopePreventive($query)
    {
        return $query->where('type', 'preventive');
    }

    public function scopeCurative($query)
    {
        return $query->where('type', 'curative');
    }

    public function scopeByVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }
}
