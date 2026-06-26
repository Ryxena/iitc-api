<?php

namespace App\Policies;

use App\Models\Competition;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CompetitionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Competition $competition): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Create Competition');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Competition $competition): bool
    {
        return $user->hasPermissionTo('Update Competition');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Competition $competition): bool
    {
        return $user->hasPermissionTo('Delete Competition');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Competition $competition): bool
    {
        return $user->hasPermissionTo('Restore Competition');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Competition $competition): bool
    {
        return $user->hasPermissionTo('Force Delete Competition');
    }
}
