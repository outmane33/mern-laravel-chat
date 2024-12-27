<?php

namespace App\Http\Controllers;

use App\Events\OnlineUsersUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SocketController extends Controller
{
    //
    public function onUserConnect($userId)
    {
        // Retrieve online users from cache
        $onlineUsers = Cache::get('onlineUsers', []);

        // Add new user to the online users list
        if (!in_array($userId, $onlineUsers)) {
            $onlineUsers[] = $userId;
        }

        // Save the updated list back to the cache
        Cache::put('onlineUsers', $onlineUsers);

        // Broadcast the updated list of online users
        // broadcast(new OnlineUsersUpdated($onlineUsers));
        event(new OnlineUsersUpdated($onlineUsers));
        return response()->json(['message' => 'User connected successfully']);
    }
    
    public function onUserDisconnect($userId)
{
    $onlineUsers = Cache::get('onlineUsers', []);
    
    // Remove user from online users list
    $onlineUsers = array_values(array_filter($onlineUsers, function($id) use ($userId) {
        return $id !== $userId;
    }));
    
    Cache::put('onlineUsers', $onlineUsers);
    
    event(new OnlineUsersUpdated($onlineUsers));
    return response()->json(['message' => 'User disconnected successfully']);
}

}
