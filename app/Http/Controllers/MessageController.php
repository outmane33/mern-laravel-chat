<?php

namespace App\Http\Controllers;

use App\Events\MyEvent;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class MessageController extends Controller
{
    //
    public function getMessages(Request $request, $id){
        
        $messages = Message::where(function($query) use ($request, $id) {
            $query->where('sender_id', $request->user()->id)
                  ->where('receiver_id', $id);
        })->orWhere(function($query) use ($request, $id) {
            $query->where('sender_id', $id)
                  ->where('receiver_id', $request->user()->id);
        })->get();

        return response()->json([
            'status' => 'success',
            'messages' => $messages
        ], 200);
    }
    public function sendMessage(Request $request,$receiver_id){ 
        $validator = Validator::make($request->all(), [
            'text' => 'sometimes',
            'image' => 'sometimes',
        ]);
                // Check if validation fails
                if ($validator->fails()) {
                    return response()->json([
                        'status' => 'error',
                        'errors' => $validator->errors()
                    ], 422);
                }

                $validateData = $validator->validated();

                $validateData['sender_id'] = $request->user()->id;
                $validateData['receiver_id'] = $receiver_id;

                if ($request->image) {
                    try {
                        // Convert base64 to file
                        $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->image));
                        
                        // Upload to Cloudinary
                        $uploadResponse = Cloudinary::upload("data:image/png;base64," . base64_encode($image));
                        $validateData['image'] = $uploadResponse->getSecurePath();
                    } catch (\Exception $e) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Failed to upload image',
                            'error' => $e->getMessage()
                        ], 500);
                    }
                }


                $message = Message::create($validateData);

                // $recipient = $request->recipient;
                event(new MyEvent($message));

                return response()->json([
                    'status' => 'success',
                    'message' => $message
                ], 200);
     }
}
