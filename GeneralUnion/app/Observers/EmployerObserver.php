<?php

namespace App\Observers;

use App\Models\Employer;
use App\Events\BroadcastingModelEvent;
class EmployerObserver
{
    /**
     * Listen to the Employer created event.
     *
     * @param  Employer $model
     * @return void
     */
    public function created(Employer $model)
    {
        event(new BroadcastingModelEvent($model->toArray(), 'created'));
    }

    public function deleting(Employer $model)
    {
        event(new BroadcastingModelEvent($model->toArray(), 'deleting'));
    }
    /**
     * Listen to the Employer deleted event. This is a special case. After
     * a model is deleted, you can't use the SerializesModels trait. 
     * @param  Employer $model
     * @return void
     */
    public function deleted(Employer $model)
    {
        event(new BroadcastingModelEvent($model->toArray(), 'deleted'));
    }
}