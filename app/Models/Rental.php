<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rental extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'employee_id',
        'rental_number',
        'client_name',
        'client_phone',
        'start_date',
        'end_date',
        'actual_return_date',
        'daily_rate',
        'total_days',
        'total_cost',
        'deposit_amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'actual_return_date' => 'date',
        'daily_rate' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'total_days' => 'integer',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeReserved($query)
    {
        return $query->where('status', 'reserved');
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'ongoing' && $this->end_date && $this->end_date->isPast();
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'reserved' => 'Réservée',
            'ongoing' => 'En Cours',
            'completed' => 'Terminée',
            'cancelled' => 'Annulée',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'reserved' => 'info',
            'ongoing' => 'success',
            'completed' => 'secondary',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($rental) {
            if (!$rental->rental_number) {
                $rental->rental_number = 'LOC-' . strtoupper(uniqid());
            }
            if ($rental->start_date && $rental->end_date && $rental->daily_rate) {
                $rental->total_days = $rental->start_date->diffInDays($rental->end_date) + 1;
                $rental->total_cost = $rental->total_days * $rental->daily_rate;
            }
        });
    }
}
