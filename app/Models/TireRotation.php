<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TireRotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'tire_id',
        'vehicle_id',
        'rotation_date',
        'from_position',
        'to_position',
        'mileage',
        'tread_depth_before',
        'tread_depth_after',
        'technician',
        'notes',
    ];

    protected $casts = [
        'rotation_date' => 'date',
        'mileage' => 'integer',
        'tread_depth_before' => 'decimal:2',
        'tread_depth_after' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function tire(): BelongsTo
    {
        return $this->belongsTo(Tire::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Accessors
     */
    public function getFromPositionLabelAttribute(): string
    {
        return match($this->from_position) {
            'front_left' => 'Avant Gauche',
            'front_right' => 'Avant Droit',
            'rear_left' => 'Arrière Gauche',
            'rear_right' => 'Arrière Droit',
            'spare' => 'Roue de Secours',
            'stock' => 'Stock',
            default => $this->from_position,
        };
    }

    public function getToPositionLabelAttribute(): string
    {
        return match($this->to_position) {
            'front_left' => 'Avant Gauche',
            'front_right' => 'Avant Droit',
            'rear_left' => 'Arrière Gauche',
            'rear_right' => 'Arrière Droit',
            'spare' => 'Roue de Secours',
            'stock' => 'Stock',
            default => $this->to_position,
        };
    }

    public function getTreadWearAttribute(): float
    {
        if (!$this->tread_depth_before || !$this->tread_depth_after) {
            return 0;
        }

        return round($this->tread_depth_before - $this->tread_depth_after, 2);
    }
}
