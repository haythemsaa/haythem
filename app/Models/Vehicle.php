<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'registration_number',
        'internal_code',
        'fleet_number',
        'brand_id',
        'vehicle_model_id',
        'vehicle_category_id',
        'parc_id',
        'site_id',
        'vin',
        'color',
        'photo',
        'year',
        'engine_type',
        'engine_power',
        'fuel_capacity',
        'tire_type',
        'length',
        'width',
        'height',
        'seats',
        'load_capacity',
        'purchase_date',
        'purchase_price',
        'acquisition_mode_id',
        'supplier_id',
        'current_mileage',
        'status',
        'sale_date',
        'sale_price',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'sale_date' => 'date',
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'load_capacity' => 'decimal:2',
    ];

    // Relations
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function vehicleModel(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(VehicleCategory::class, 'vehicle_category_id');
    }

    public function parc(): BelongsTo
    {
        return $this->belongsTo(Parc::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function acquisitionMode(): BelongsTo
    {
        return $this->belongsTo(AcquisitionMode::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function acquisitionContracts(): HasMany
    {
        return $this->hasMany(AcquisitionContract::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(VehicleAssignment::class);
    }

    public function fuelConsumptions(): HasMany
    {
        return $this->hasMany(FuelConsumption::class);
    }

    public function maintenancePlans(): HasMany
    {
        return $this->hasMany(PreventiveMaintenancePlan::class);
    }

    public function interventions(): HasMany
    {
        return $this->hasMany(Intervention::class);
    }

    public function insurances(): HasMany
    {
        return $this->hasMany(Insurance::class);
    }

    public function technicalInspections(): HasMany
    {
        return $this->hasMany(TechnicalInspection::class);
    }

    public function legalDocuments(): HasMany
    {
        return $this->hasMany(LegalDocument::class);
    }

    public function transportMissions(): HasMany
    {
        return $this->hasMany(TransportMission::class);
    }

    public function rentalContracts(): HasMany
    {
        return $this->hasMany(RentalContract::class);
    }

    public function gpsTracker(): HasMany
    {
        return $this->hasMany(GpsTracker::class);
    }

    public function gpsAlerts(): HasMany
    {
        return $this->hasMany(GpsAlert::class);
    }

    public function gpsLocations(): HasMany
    {
        return $this->hasMany(GpsLocation::class);
    }

    public function latestGpsLocation()
    {
        return $this->hasOne(GpsLocation::class)->latestOfMany('recorded_at');
    }

    // Accessors & Mutators
    public function getFullNameAttribute(): string
    {
        return "{$this->brand->name} {$this->vehicleModel->name} - {$this->registration_number}";
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->status === 'disponible';
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'disponible');
    }

    public function scopeInMaintenance($query)
    {
        return $query->where('status', 'en_maintenance');
    }

    public function scopeInMission($query)
    {
        return $query->where('status', 'en_mission');
    }
}
