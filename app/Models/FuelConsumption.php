<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FuelConsumption extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'employee_id',
        'fuel_type_id',
        'supplier_id',
        'refueling_date',
        'quantity',
        'unit_price',
        'total_amount',
        'mileage',
        'previous_mileage',
        'distance_covered',
        'consumption_rate',
        'is_full_tank',
        'invoice_number',
        'pump_number',
        'fuel_card_number',
        'location',
        'notes',
    ];

    protected $casts = [
        'refueling_date' => 'date',
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:3',
        'total_amount' => 'decimal:2',
        'mileage' => 'integer',
        'previous_mileage' => 'integer',
        'distance_covered' => 'integer',
        'consumption_rate' => 'decimal:2',
        'is_full_tank' => 'boolean',
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

    public function fuelType(): BelongsTo
    {
        return $this->belongsTo(FuelType::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    // Accessors
    public function getConsumptionPer100KmAttribute(): ?float
    {
        if ($this->distance_covered > 0 && $this->quantity > 0) {
            return round(($this->quantity / $this->distance_covered) * 100, 2);
        }
        return null;
    }

    public function getCostPerKmAttribute(): ?float
    {
        if ($this->distance_covered > 0 && $this->total_amount > 0) {
            return round($this->total_amount / $this->distance_covered, 3);
        }
        return null;
    }

    // Scopes
    public function scopeByVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function scopeFullTankOnly($query)
    {
        return $query->where('is_full_tank', true);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('refueling_date', [$startDate, $endDate]);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('refueling_date', '>=', now()->subDays($days));
    }

    // Mutators
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($fuelConsumption) {
            // Auto-calculate total if not provided
            if (!$fuelConsumption->total_amount && $fuelConsumption->quantity && $fuelConsumption->unit_price) {
                $fuelConsumption->total_amount = $fuelConsumption->quantity * $fuelConsumption->unit_price;
            }

            // Auto-calculate distance if mileages are provided
            if ($fuelConsumption->mileage && $fuelConsumption->previous_mileage) {
                $fuelConsumption->distance_covered = $fuelConsumption->mileage - $fuelConsumption->previous_mileage;
            }

            // Auto-calculate consumption rate if full tank
            if ($fuelConsumption->is_full_tank && $fuelConsumption->distance_covered > 0) {
                $fuelConsumption->consumption_rate = round(
                    ($fuelConsumption->quantity / $fuelConsumption->distance_covered) * 100,
                    2
                );
            }
        });
    }
}
