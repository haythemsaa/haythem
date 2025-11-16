<?php

namespace App\Policies;

use App\Models\Intervention;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InterventionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any interventions.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_interventions');
    }

    /**
     * Determine whether the user can view the intervention.
     */
    public function view(User $user, Intervention $intervention): bool
    {
        return $user->hasPermissionTo('view_interventions');
    }

    /**
     * Determine whether the user can create interventions.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_interventions');
    }

    /**
     * Determine whether the user can update the intervention.
     */
    public function update(User $user, Intervention $intervention): bool
    {
        // Only allow editing if intervention is not closed or if user is admin
        if ($intervention->status === 'terminee' && !$user->hasRole('Super Admin')) {
            return false;
        }

        return $user->hasPermissionTo('edit_interventions');
    }

    /**
     * Determine whether the user can delete the intervention.
     */
    public function delete(User $user, Intervention $intervention): bool
    {
        // Only allow deletion if intervention is pending or if user is admin
        if ($intervention->status !== 'en_attente' && !$user->hasRole('Super Admin')) {
            return false;
        }

        return $user->hasPermissionTo('delete_interventions');
    }

    /**
     * Determine whether the user can restore the intervention.
     */
    public function restore(User $user, Intervention $intervention): bool
    {
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine whether the user can permanently delete the intervention.
     */
    public function forceDelete(User $user, Intervention $intervention): bool
    {
        return $user->hasRole('Super Admin');
    }

    /**
     * Determine whether the user can validate the intervention.
     */
    public function validate(User $user, Intervention $intervention): bool
    {
        return $user->hasPermissionTo('validate_interventions')
            || $user->hasRole(['Manager', 'Super Admin']);
    }

    /**
     * Determine whether the user can close the intervention.
     */
    public function close(User $user, Intervention $intervention): bool
    {
        return $user->hasPermissionTo('edit_interventions')
            && $intervention->status === 'en_cours';
    }
}
