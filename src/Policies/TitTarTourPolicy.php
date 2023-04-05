<?php

namespace Local\CMS\Policies;

use App\Models\TitTarTour;
use Local\CMS\Models\Admin as User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TitTarTourPolicy
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
        return $user->can(['tit_tar_tour_read']);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TitTarTour  $tit_tar_tour
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, TitTarTour $tit_tar_tour)
    {
        return $user->can(['tit_tar_tour_read']);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->can(['tit_tar_tour_read', 'tit_tar_tour_create']);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TitTarTour  $tit_tar_tour
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, TitTarTour $tit_tar_tour)
    {
        return $user->can(['tit_tar_tour_read', 'tit_tar_tour_update']);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TitTarTour  $tit_tar_tour
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, TitTarTour $tit_tar_tour)
    {
        return $user->can(['tit_tar_tour_read', 'tit_tar_tour_delete']);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TitTarTour  $tit_tar_tour
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, TitTarTour $tit_tar_tour)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TitTarTour  $tit_tar_tour
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, TitTarTour $tit_tar_tour)
    {
        //
    }
}
