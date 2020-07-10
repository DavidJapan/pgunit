<?php
 
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class Gudb0605Event implements  ShouldBroadcast{

    use Dispatchable,
        InteractsWithSockets,
        SerializesModels;

    public $message;

    public function __construct($message) {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn() {
        return new Channel('gudb0605Channel');
    }



    public function broadcastAs() {
        return 'Gudb0605Event';
    }

}
