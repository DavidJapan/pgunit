<?php

namespace App\Observers;

use App\Models\Report;
use App\Events\BroadcastingModelEvent;
class ReportObserver
{
    /**
     * Listen to the Report created event.
     *
     * @param  Report $model
     * @return void
     */
    public function created(Report $model)
    {
        event(new BroadcastingModelEvent($model->toArray(), 'created'));
    }

    public function deleting(Report $model)
    {
        event(new BroadcastingModelEvent($model->toArray(), 'deleting'));
    }
    /**
     * Listen to the Report deleted event. This is a special case. After
     * a model is deleted, you can't use the SerializesModels trait. 
     * @param  Report $model
     * @return void
     */
    public function deleted(Report $model)
    {
        event(new BroadcastingModelEvent($model->toArray(), 'deleted'));
    }
}