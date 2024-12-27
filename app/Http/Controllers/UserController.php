<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class UserController extends Controller
{
    //
    private function sanitizeUser($user)
    {
        return [
            'id' => $user->id,
            'full_name' => $user->full_name,
            'email' => $user->email,
            'profilePic' => $user->profilePic,
        ];
    }

    public function getUsersForSidebar(Request $request)
    {
        $users = User::where('id', '!=', $request->user()->id)->get();
        
        return response()->json([
            'status' => 'success',
            'users' => $users->map(fn($user) => $this->sanitizeUser($user))
        ], 200);
    }
    public function  updateProfile(Request $request)
    {
        $user = $request->user();
    
        if ($request->has('profilePic')) {
            try {
                // Decode base64 image
                $profilePic = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->profilePic));
    
                // Upload to Cloudinary
                $uploadResponse = Cloudinary::upload("data:image/png;base64," . base64_encode($profilePic));
                $imagePath = $uploadResponse->getSecurePath();
    
                // Update user profile picture
                $user->profilePic = $imagePath;
                $user->save();
    
                return response()->json([
                    'status' => 'success',
                    'user' => $user,
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to upload image',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }
    
        return response()->json([
            'status' => 'error',
            'message' => 'No profile picture provided',
        ], 400);
    }
    
}
