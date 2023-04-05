<?php

namespace Local\CMS\Policies;

use App\Models\Outlet;
use Local\CMS\Models\Admin as User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OutletPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->can(['outlet_read']);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Outlet $outlet)
    {
        return $user->can(['outlet_read']);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->can(['outlet_read', 'outlet_create']);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Outlet $outlet)
    {
        return $user->can(['outlet_read', 'outlet_update']);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Outlet $outlet)
    {
        return $user->can(['outlet_read', 'outlet_delete']);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Outlet $outlet)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Outlet $outlet)
    {
        //
    }
}
