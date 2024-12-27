<?php

use Illuminate\Support\Facades\Broadcast;

/*
 |--------------------------------------------------------------------------
 | Broadcast Channels
 |--------------------------------------------------------------------------
 |
 | Here you may register all of the event broadcasting channels that your
 | application supports. The given channel authorization callbacks are
 | used to check if an authenticated user can listen to the channel.
 |
 */
Broadcast::channel('online-users', function ($user) {
    return true; // Allow all users for simplicity. You can add additional checks here.
});

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});