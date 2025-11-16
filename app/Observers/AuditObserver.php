<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        if ($this->shouldAudit($model)) {
            $this->log('created', $model, [], $model->getAttributes());
        }
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        if ($this->shouldAudit($model)) {
            $this->log('updated', $model, $model->getOriginal(), $model->getChanges());
        }
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        if ($this->shouldAudit($model)) {
            $this->log('deleted', $model, $model->getAttributes(), []);
        }
    }

    /**
     * Handle the Model "restored" event.
     */
    public function restored(Model $model): void
    {
        if ($this->shouldAudit($model)) {
            $this->log('restored', $model, [], $model->getAttributes());
        }
    }

    /**
     * Log the audit entry
     */
    protected function log(string $event, Model $model, array $oldValues, array $newValues): void
    {
        $user = Auth::user();

        AuditLog::create([
            'user_type' => $user ? get_class($user) : null,
            'user_id' => $user?->id,
            'event' => $event,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id,
            'old_values' => $this->filterValues($oldValues),
            'new_values' => $this->filterValues($newValues),
            'url' => request()->fullUrl(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Filter sensitive data from values
     */
    protected function filterValues(array $values): array
    {
        $sensitiveFields = ['password', 'password_confirmation', 'remember_token'];

        foreach ($sensitiveFields as $field) {
            if (isset($values[$field])) {
                $values[$field] = '***HIDDEN***';
            }
        }

        return $values;
    }

    /**
     * Determine if the model should be audited
     */
    protected function shouldAudit(Model $model): bool
    {
        // Don't audit AuditLog itself to avoid recursion
        if ($model instanceof AuditLog) {
            return false;
        }

        // Check if audit is enabled in settings
        if (!setting('security.audit_log_enabled', true)) {
            return false;
        }

        return true;
    }
}
