<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledReport extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'report_type',
        'frequency',
        'format',
        'recipients',
        'filters',
        'is_active',
        'next_run_at',
        'last_run_at',
    ];

    protected $casts = [
        'recipients' => 'array',
        'filters' => 'array',
        'is_active' => 'boolean',
        'next_run_at' => 'datetime',
        'last_run_at' => 'datetime',
    ];

    /**
     * Get the user that owns the scheduled report
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get frequency label
     */
    public function getFrequencyLabelAttribute(): string
    {
        return match($this->frequency) {
            'daily' => 'Quotidien',
            'weekly' => 'Hebdomadaire',
            'monthly' => 'Mensuel',
            'quarterly' => 'Trimestriel',
            default => ucfirst($this->frequency)
        };
    }

    /**
     * Get report type label
     */
    public function getReportTypeLabelAttribute(): string
    {
        return match($this->report_type) {
            'fleet' => 'Flotte',
            'maintenance' => 'Maintenance',
            'fuel' => 'Carburant',
            'financial' => 'Financier',
            default => ucfirst($this->report_type)
        };
    }
}
