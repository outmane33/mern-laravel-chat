<?php

namespace App\Models\Services;

use App\Events\OnlineUsersUpdated;
use Illuminate\Database\Eloquent\Model;

class UserSocketManager
{
    protected static $userSocketMap = [];

    public static function addUser($userId, $socketId)
    {
        self::$userSocketMap[$userId] = $socketId;
        self::broadcastOnlineUsers();
    }

    public static function removeUser($userId)
    {
        unset(self::$userSocketMap[$userId]);
        self::broadcastOnlineUsers();
    }

    public static function getReceiverSocketId($userId)
    {
        return self::$userSocketMap[$userId] ?? null;
    }

    public static function broadcastOnlineUsers()
    {
        $onlineUsers = array_keys(self::$userSocketMap);
        broadcast(new OnlineUsersUpdated($onlineUsers));
    }
}
