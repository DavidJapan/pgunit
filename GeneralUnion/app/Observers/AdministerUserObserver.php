<?php

namespace App\Observers;

use App\Models\AdministerUser;
use App\Events\BroadcastingModelEvent;
class AdministerUserObserver
{
    /**
     * Listen to the AdministerUser created event.
     *
     * @param  AdministerUser $user
     * @return void
     */
    public function created(AdministerUser $user)
    {
        event(new BroadcastingModelEvent($user, 'created'));
    }

    /**
     * Listen to the AdministerUser deleting event.
     *
     * @param  AdministerUser $user
     * @return void
     */
    public function deleting(AdministerUser $user)
    {
        //
    }
}