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
        'supplier_code',
        'supplier_type',
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'country',
        'tax_id',
        'payment_terms',
        'status',
        'notes',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'active' => 'Actif',
            'inactive' => 'Inactif',
            'suspended' => 'Suspendu',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'secondary',
            'suspended' => 'warning',
            default => 'secondary',
        };
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($supplier) {
            if (!$supplier->supplier_code) {
                $supplier->supplier_code = 'SUP-' . strtoupper(substr(uniqid(), -6));
            }
        });
    }
}
