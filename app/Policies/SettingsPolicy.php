<?php

namespace App\Policies;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SettingsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any settings.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_settings')
            || $user->hasRole(['Manager', 'Super Admin']);
    }

    /**
     * Determine whether the user can view the setting.
     */
    public function view(User $user, Setting $setting): bool
    {
        return $user->hasPermissionTo('view_settings')
            || $user->hasRole(['Manager', 'Super Admin']);
    }

    /**
     * Determine whether the user can update settings.
     */
    public function update(User $user): bool
    {
        return $user->hasPermissionTo('edit_settings')
            || $user->hasRole(['Manager', 'Super Admin']);
    }

    /**
     * Determine whether the user can update critical system settings.
     */
    public function updateCritical(User $user): bool
    {
        // Only Super Admin can modify critical settings like security, email, etc.
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine whether the user can update application settings.
     */
    public function updateApp(User $user): bool
    {
        return $user->hasPermissionTo('edit_settings')
            || $user->hasRole(['Manager', 'Super Admin']);
    }

    /**
     * Determine whether the user can update fleet settings.
     */
    public function updateFleet(User $user): bool
    {
        return $user->hasPermissionTo('edit_settings')
            || $user->hasRole(['Manager', 'Super Admin']);
    }

    /**
     * Determine whether the user can update notification settings.
     */
    public function updateNotifications(User $user): bool
    {
        return $user->hasPermissionTo('edit_settings')
            || $user->hasRole(['Manager', 'Super Admin']);
    }

    /**
     * Determine whether the user can clear cache.
     */
    public function clearCache(User $user): bool
    {
        return $user->hasRole(['Manager', 'Super Admin']);
    }

    /**
     * Determine whether the user can enable/disable maintenance mode.
     */
    public function toggleMaintenance(User $user): bool
    {
        return $user->hasRole('Super Admin');
    }
}
