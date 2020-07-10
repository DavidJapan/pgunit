<?php

namespace App\Observers;

use App\Models\ReportHeading;
use App\Events\BroadcastingModelEvent;
class ReportHeadingObserver
{
    /**
     * Listen to the ReportHeading created event.
     *
     * @param  ReportHeading $model
     * @return void
     */
    public function created(ReportHeading $model)
    {
        event(new BroadcastingModelEvent($model->toArray(), 'created'));
    }

    public function deleting(ReportHeading $model)
    {
        event(new BroadcastingModelEvent($model->toArray(), 'deleting'));
    }
    /**
     * Listen to the ReportHeading deleted event. This is a special case. After
     * a model is deleted, you can't use the SerializesModels trait. 
     * @param  ReportHeading $model
     * @return void
     */
    public function deleted(ReportHeading $model)
    {
        event(new BroadcastingModelEvent($model->toArray(), 'deleted'));
    }
}