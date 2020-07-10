<?php

/*
 * https://github.com/laravel/framework/issues/10733
 * 
 */

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Model as Model;

/**
 * https://github.com/laravel/framework/issues/10733
 */
class BroadcastingModelEvent implements ShouldBroadcast {

    use Dispatchable,
        InteractsWithSockets;
    //Throws an exception after the deleted event.
        //SerializesModels;

    /**
     * This is not an instance of an Eloquent model, but a serialised version
     * of the model created by calling toArray() on the model in its event handler.
     * @var array
     */
    public $model;
    public $eventType;

    
    public function __construct($model, $eventType) {
        $this->model = $model;
        $this->eventType = $eventType;
    }

    public function broadcastOn() {
        return new Channel('gudb0605BroadcastObservers');
    }

    public function broadcastAs() {
        return 'gudb0605BroadcastEvent';
    }

}
