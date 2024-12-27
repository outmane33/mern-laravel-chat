<?php

namespace App\Http\Controllers;

use App\Events\MyEvent;
use Illuminate\Http\Request;

class SendNotificationController extends Controller
{
    //
    public function store(Request $request)
    {
   
            
            // $message = "New user registration: {$request->firstName} {$request->LastName}";
            $message =[
                'sender' => $request->sender,
                'message' => $request->message,
                'receiver_id' => $request->receiver_id
            ];
            // $recipient = $request->recipient;
            event(new MyEvent($message));
            
            
            return response()->json([
                'status' => 'success',
                'message' => 'Notification sent!',
                'debug' => [
                    'messageContent' => $message,
                    'channel' => 'chatapp',
                    'event' => 'message'
                ]
            ]);

    }
}
