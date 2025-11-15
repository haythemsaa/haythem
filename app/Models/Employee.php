<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'employee_code',
        'photo',
        'birth_date',
        'phone',
        'email',
        'address',
        'city',
        'country',
        'position',
        'department',
        'site_id',
        'supervisor_id',
        'hire_date',
        'contract_type',
        'base_salary',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'hire_date' => 'date',
        'base_salary' => 'decimal:2',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'supervisor_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'supervisor_id');
    }

    public function drivingLicense(): HasOne
    {
        return $this->hasOne(DrivingLicense::class);
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class);
    }

    public function trafficViolations(): HasMany
    {
        return $this->hasMany(TrafficViolation::class);
    }

    public function accidents(): HasMany
    {
        return $this->hasMany(Accident::class);
    }

    public function trainings(): HasMany
    {
        return $this->hasMany(Training::class);
    }

    public function medicalCheckups(): HasMany
    {
        return $this->hasMany(MedicalCheckup::class);
    }

    public function epi(): HasMany
    {
        return $this->hasMany(Epi::class);
    }

    public function vehicleAssignments(): HasMany
    {
        return $this->hasMany(VehicleAssignment::class);
    }

    public function interventions(): HasMany
    {
        return $this->hasMany(Intervention::class);
    }

    public function transportMissions(): HasMany
    {
        return $this->hasMany(TransportMission::class, 'driver_id');
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'actif';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'actif');
    }

    public function scopeDrivers($query)
    {
        return $query->whereHas('drivingLicense');
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }
}
