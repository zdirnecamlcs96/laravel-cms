<?php

namespace Local\CMS\Policies;

use Local\CMS\Models\NewsEvent;
use Local\CMS\Models\Admin as User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NewsEventPolicy
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
        return $user->can('news_event_read');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\NewsEvent  $newsEvent
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, NewsEvent $newsEvent)
    {
        return $user->can('news_event_read');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->can(['news_event_read', 'news_event_create']);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\NewsEvent  $newsEvent
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, NewsEvent $newsEvent)
    {
        // return true;
        return $user->can(['news_event_read', 'news_event_update']);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\NewsEvent  $newsEvent
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, NewsEvent $newsEvent)
    {
        return $user->can(['news_event_read', 'news_event_delete']);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\NewsEvent  $newsEvent
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, NewsEvent $newsEvent)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\NewsEvent  $newsEvent
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, NewsEvent $newsEvent)
    {
        //
    }
}
