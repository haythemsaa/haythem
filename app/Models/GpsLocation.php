<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GpsLocation extends Model
{
    protected $fillable = [
        'vehicle_id',
        'latitude',
        'longitude',
        'speed',
        'heading',
        'altitude',
        'accuracy',
        'address',
        'recorded_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'speed' => 'decimal:2',
        'altitude' => 'decimal:2',
        'accuracy' => 'decimal:2',
        'recorded_at' => 'datetime',
    ];

    /**
     * Get the vehicle that owns this GPS location
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get formatted coordinates
     */
    public function getCoordinatesAttribute(): string
    {
        return "{$this->latitude}, {$this->longitude}";
    }

    /**
     * Get Google Maps link
     */
    public function getGoogleMapsLinkAttribute(): string
    {
        return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
    }
}
