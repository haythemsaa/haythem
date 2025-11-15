<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Training extends Model
{
    protected $fillable = [
        'employee_id',
        'title',
        'description',
        'training_type',
        'organization',
        'start_date',
        'end_date',
        'duration_hours',
        'cost',
        'result',
        'evaluation',
        'certificate',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'duration_hours' => 'integer',
        'cost' => 'decimal:2',
    ];

    // Relations
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // Accessors
    public function getIsCompletedAttribute(): bool
    {
        if (!$this->end_date) {
            return false;
        }
        return $this->end_date->isPast();
    }

    public function getIsOngoingAttribute(): bool
    {
        if (!$this->start_date || !$this->end_date) {
            return false;
        }
        return $this->start_date->isPast() && $this->end_date->isFuture();
    }

    public function getIsUpcomingAttribute(): bool
    {
        if (!$this->start_date) {
            return false;
        }
        return $this->start_date->isFuture();
    }

    public function getStatusAttribute(): string
    {
        if ($this->is_completed) {
            return 'completed';
        }
        if ($this->is_ongoing) {
            return 'ongoing';
        }
        if ($this->is_upcoming) {
            return 'upcoming';
        }
        return 'unknown';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'completed' => 'Terminée',
            'ongoing' => 'En Cours',
            'upcoming' => 'À Venir',
            default => 'Indéfini',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'completed' => 'success',
            'ongoing' => 'primary',
            'upcoming' => 'info',
            default => 'secondary',
        };
    }

    public function getResultLabelAttribute(): ?string
    {
        if (!$this->result) {
            return null;
        }

        return match(strtolower($this->result)) {
            'reussi', 'réussi', 'passed', 'success' => 'Réussi',
            'echoue', 'échoué', 'failed', 'fail' => 'Échoué',
            'en_cours', 'ongoing', 'in_progress' => 'En Cours',
            'abandonne', 'abandonné', 'abandoned' => 'Abandonné',
            default => ucfirst($this->result),
        };
    }

    public function getResultColorAttribute(): string
    {
        if (!$this->result) {
            return 'secondary';
        }

        $lowerResult = strtolower($this->result);
        if (in_array($lowerResult, ['reussi', 'réussi', 'passed', 'success'])) {
            return 'success';
        }
        if (in_array($lowerResult, ['echoue', 'échoué', 'failed', 'fail'])) {
            return 'danger';
        }
        if (in_array($lowerResult, ['en_cours', 'ongoing', 'in_progress'])) {
            return 'primary';
        }
        return 'warning';
    }

    public function getDurationDaysAttribute(): ?int
    {
        if (!$this->start_date || !$this->end_date) {
            return null;
        }
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('end_date')
                    ->where('end_date', '<', now());
    }

    public function scopeOngoing($query)
    {
        return $query->whereNotNull('start_date')
                    ->whereNotNull('end_date')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->whereNotNull('start_date')
                    ->where('start_date', '>', now());
    }

    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('training_type', 'like', "%{$type}%");
    }

    public function scopeByResult($query, $result)
    {
        return $query->where('result', 'like', "%{$result}%");
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate]);
        });
    }
}
