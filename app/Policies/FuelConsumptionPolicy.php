<?php

namespace App\Policies;

use App\Models\FuelConsumption;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FuelConsumptionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any fuel consumptions.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_fuel_consumptions');
    }

    /**
     * Determine whether the user can view the fuel consumption.
     */
    public function view(User $user, FuelConsumption $fuelConsumption): bool
    {
        return $user->hasPermissionTo('view_fuel_consumptions');
    }

    /**
     * Determine whether the user can create fuel consumptions.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_fuel_consumptions');
    }

    /**
     * Determine whether the user can update the fuel consumption.
     */
    public function update(User $user, FuelConsumption $fuelConsumption): bool
    {
        // Only allow editing within 30 days or if user is admin
        $isRecent = $fuelConsumption->created_at->diffInDays(now()) <= 30;

        if (!$isRecent && !$user->hasRole('Super Admin')) {
            return false;
        }

        return $user->hasPermissionTo('edit_fuel_consumptions');
    }

    /**
     * Determine whether the user can delete the fuel consumption.
     */
    public function delete(User $user, FuelConsumption $fuelConsumption): bool
    {
        // Only allow deletion within 7 days or if user is admin
        $isVeryRecent = $fuelConsumption->created_at->diffInDays(now()) <= 7;

        if (!$isVeryRecent && !$user->hasRole('Super Admin')) {
            return false;
        }

        return $user->hasPermissionTo('delete_fuel_consumptions');
    }

    /**
     * Determine whether the user can restore the fuel consumption.
     */
    public function restore(User $user, FuelConsumption $fuelConsumption): bool
    {
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine whether the user can permanently delete the fuel consumption.
     */
    public function forceDelete(User $user, FuelConsumption $fuelConsumption): bool
    {
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine whether the user can export fuel consumption data.
     */
    public function export(User $user): bool
    {
        return $user->hasPermissionTo('export_fuel_consumptions')
            || $user->hasRole(['Manager', 'Super Admin']);
    }

    /**
     * Determine whether the user can view fuel analytics.
     */
    public function viewAnalytics(User $user): bool
    {
        return $user->hasPermissionTo('view_fuel_analytics')
            || $user->hasRole(['Manager', 'Super Admin']);
    }
}
