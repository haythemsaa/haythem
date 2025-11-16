<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'code', 'address', 'city', 'country',
        'phone', 'email', 'is_main', 'is_active'
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeMain($query)
    {
        return $query->where('is_main', true);
    }

    // Boot
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($site) {
            if (!$site->code) {
                $site->code = 'SITE-' . strtoupper(uniqid());
            }
        });
    }
}
