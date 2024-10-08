<?php

namespace App\Policies;

use App\Models\Plate;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PlatePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Plate $plate): bool
    {
        return $user->restaurants()->where("id", $plate->restaurant_id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Plate $plate): bool
    {
        return $user->restaurants()->where("id", $plate->restaurant_id)->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Plate $plate): bool
    {
        return $user->restaurants()->where("id", $plate->restaurant_id)->exists();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Plate $plate): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Plate $plate): bool
    {
        //
    }
}
