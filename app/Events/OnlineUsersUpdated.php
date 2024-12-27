<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OnlineUsersUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $onlineUsers;

    public function __construct(array $onlineUsers)
    {
        $this->onlineUsers = $onlineUsers;
    }


    public function broadcastOn()
    {
        // return new Channel('online-users');
        return ['online-users'];
    }

    public function broadcastAs()
    {
        return 'getOnlineUsers';
    }

    public function broadcastWith()
    {
        return [
            'onlineUsers' => $this->onlineUsers,
            'time' => now()->toDateTimeString()
        ];
    }


}
