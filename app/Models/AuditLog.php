<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'user_type',
        'user_id',
        'event',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'url',
        'ip_address',
        'user_agent',
        'tags',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'tags' => 'array',
    ];

    /**
     * Get the user that performed the action
     */
    public function user(): MorphTo
    {
        return $this->morphTo('user');
    }

    /**
     * Get the auditable model
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get formatted event name
     */
    public function getEventLabelAttribute(): string
    {
        return match($this->event) {
            'created' => 'Création',
            'updated' => 'Modification',
            'deleted' => 'Suppression',
            'restored' => 'Restauration',
            default => ucfirst($this->event)
        };
    }

    /**
     * Get changes as array
     */
    public function getChangesAttribute(): array
    {
        $changes = [];

        if ($this->event === 'created') {
            foreach ($this->new_values ?? [] as $key => $value) {
                $changes[$key] = [
                    'old' => null,
                    'new' => $value
                ];
            }
        } elseif ($this->event === 'updated') {
            foreach ($this->new_values ?? [] as $key => $newValue) {
                $oldValue = $this->old_values[$key] ?? null;
                if ($oldValue != $newValue) {
                    $changes[$key] = [
                        'old' => $oldValue,
                        'new' => $newValue
                    ];
                }
            }
        } elseif ($this->event === 'deleted') {
            foreach ($this->old_values ?? [] as $key => $value) {
                $changes[$key] = [
                    'old' => $value,
                    'new' => null
                ];
            }
        }

        return $changes;
    }
}
