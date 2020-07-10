<?php

namespace App\Observers;

use App\Models\AdministerRole;
use App\Events\BroadcastingModelEvent;
/**
 * https://github.com/laravel/framework/issues/10733
 * The deleted event is a special case.
 */
class AdministerRoleObserver
{
    /**
     * Listen to the AdministerRole created event.
     *
     * @param  AdministerRole $role
     * @return void
     */
    public function created(AdministerRole $role)
    {
        event(new BroadcastingModelEvent($role->toArray(), 'created'));
    }

    public function deleting(AdministerRole $role)
    {
        event(new BroadcastingModelEvent($role->toArray(), 'deleting'));
    }
    /**
     * Listen to the AdministerRole deleted event. This is a special case. After
     * a model is deleted, you can't use the SerializesModels trait. 
     * @param  AdministerRole $role
     * @return void
     */
    public function deleted(AdministerRole $role)
    {
        //This works
        //$arg_list = func_get_args();
        event(new BroadcastingModelEvent($role->toArray(), 'deleted'));
        //This throws an exception
        //event(new BroadcastingModelEvent($role, 'deleted'));
        //No query results for model [App\Models\AdministerRole].
        //There's a clue here: https://github.com/laravel/framework/issues/9347 related to serializing models.
        
    }
}