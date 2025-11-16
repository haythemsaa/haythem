<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'type',
        'address',
        'city',
        'country',
        'phone',
        'email',
        'tax_id',
        'bank_account',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Actif' : 'Inactif';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->is_active ? 'success' : 'secondary';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($supplier) {
            if (!$supplier->code) {
                $supplier->code = 'SUP-' . strtoupper(substr(uniqid(), -6));
            }
        });
    }
}
