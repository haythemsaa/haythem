<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryPart extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'part_number',
        'name',
        'description',
        'category',
        'supplier_id',
        'unit_price',
        'quantity_in_stock',
        'minimum_stock',
        'maximum_stock',
        'location',
        'status',
        'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity_in_stock' => 'integer',
        'minimum_stock' => 'integer',
        'maximum_stock' => 'integer',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity_in_stock', '<=', 'minimum_stock');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('quantity_in_stock', '<=', 0);
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->quantity_in_stock <= $this->minimum_stock;
    }

    public function getIsOutOfStockAttribute(): bool
    {
        return $this->quantity_in_stock <= 0;
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->is_out_of_stock) return 'out_of_stock';
        if ($this->is_low_stock) return 'low_stock';
        if ($this->quantity_in_stock >= $this->maximum_stock) return 'overstock';
        return 'normal';
    }

    public function getStockStatusColorAttribute(): string
    {
        return match($this->stock_status) {
            'out_of_stock' => 'danger',
            'low_stock' => 'warning',
            'overstock' => 'info',
            'normal' => 'success',
            default => 'secondary',
        };
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($part) {
            if (!$part->part_number) {
                $part->part_number = 'PART-' . strtoupper(substr(uniqid(), -8));
            }
        });
    }
}
