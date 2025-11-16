<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tire extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'site_id',
        'tire_code',
        'brand',
        'model',
        'size',
        'tire_type',
        'position',
        'purchase_date',
        'installation_date',
        'purchase_cost',
        'initial_tread_depth',
        'current_tread_depth',
        'mileage_at_installation',
        'current_mileage',
        'status',
        'removal_date',
        'removal_reason',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'installation_date' => 'date',
        'removal_date' => 'date',
        'purchase_cost' => 'decimal:2',
        'initial_tread_depth' => 'decimal:2',
        'current_tread_depth' => 'decimal:2',
        'mileage_at_installation' => 'integer',
        'current_mileage' => 'integer',
    ];

    /**
     * Relationships
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function rotations(): HasMany
    {
        return $this->hasMany(TireRotation::class);
    }

    /**
     * Scopes
     */
    public function scopeInstalled($query)
    {
        return $query->where('status', 'installed');
    }

    public function scopeInStock($query)
    {
        return $query->where('status', 'in_stock');
    }

    public function scopeWornOut($query)
    {
        return $query->where('status', 'worn_out');
    }

    public function scopeCriticalWear($query)
    {
        return $query->where('current_tread_depth', '<=', 2.0)
                     ->where('status', 'installed');
    }

    public function scopeLowWear($query)
    {
        return $query->whereBetween('current_tread_depth', [2.0, 3.0])
                     ->where('status', 'installed');
    }

    public function scopeByVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeBySite($query, $siteId)
    {
        return $query->where('site_id', $siteId);
    }

    /**
     * Accessors
     */
    public function getWearPercentageAttribute(): float
    {
        if (!$this->initial_tread_depth || $this->initial_tread_depth <= 0) {
            return 0;
        }

        $wearPercentage = (($this->initial_tread_depth - $this->current_tread_depth) / $this->initial_tread_depth) * 100;
        return round($wearPercentage, 2);
    }

    public function getRemainingTreadAttribute(): float
    {
        return $this->current_tread_depth ?? 0;
    }

    public function getMileageTraveledAttribute(): int
    {
        if (!$this->current_mileage || !$this->mileage_at_installation) {
            return 0;
        }

        return $this->current_mileage - $this->mileage_at_installation;
    }

    public function getWearStatusAttribute(): string
    {
        if ($this->status !== 'installed') {
            return 'inactive';
        }

        if ($this->current_tread_depth <= 1.6) {
            return 'critical'; // Below legal limit in most countries
        }

        if ($this->current_tread_depth <= 2.0) {
            return 'danger';
        }

        if ($this->current_tread_depth <= 3.0) {
            return 'warning';
        }

        return 'good';
    }

    public function getWearColorAttribute(): string
    {
        return match($this->wear_status) {
            'critical' => 'danger',
            'danger' => 'danger',
            'warning' => 'warning',
            'good' => 'success',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'in_stock' => 'En Stock',
            'installed' => 'Installé',
            'worn_out' => 'Usé',
            'damaged' => 'Endommagé',
            'scrapped' => 'Mis au rebut',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'in_stock' => 'info',
            'installed' => 'success',
            'worn_out' => 'warning',
            'damaged' => 'danger',
            'scrapped' => 'secondary',
            default => 'secondary',
        };
    }

    public function getPositionLabelAttribute(): string
    {
        return match($this->position) {
            'front_left' => 'Avant Gauche',
            'front_right' => 'Avant Droit',
            'rear_left' => 'Arrière Gauche',
            'rear_right' => 'Arrière Droit',
            'spare' => 'Roue de Secours',
            'stock' => 'Stock',
            default => $this->position,
        };
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tire) {
            if (!$tire->tire_code) {
                $tire->tire_code = 'TIRE-' . strtoupper(uniqid());
            }

            // Set initial tread depth if not provided
            if (!$tire->initial_tread_depth) {
                $tire->initial_tread_depth = 8.0; // Default new tire tread depth in mm
            }

            // Set current tread depth to initial if not provided
            if (!$tire->current_tread_depth) {
                $tire->current_tread_depth = $tire->initial_tread_depth;
            }
        });
    }
}
