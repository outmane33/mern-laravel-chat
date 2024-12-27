<?php

namespace App\Http\Controllers;

use App\Models\User;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    //
    function login(Request $request) 
    {
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
          return response()->json([
            "status"=>"failed",
          ], 401);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
          "status" => "success",
          "user" => $user
          ])->cookie('auth_token', $token, 60 * 24); // Cookie will last for 24 hours
    }
    function register(Request $request)
    {
        try {
            $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users,email',
                'password' => 'required|string|min:6'
            ]);

            $user = User::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json(["status" => "success", "user" => $user], 201)->cookie('auth_token', $token, 60 * 24); // Cookie will last for 24 hours;
        } catch (ValidationException $e) {
            return response()->json([
                
            ], 422);
        }
    }
    function logout(Request $request)
    {
        $token = $request->cookie('auth_token');
    
        if (!$token) {
            return response()->json(['error' => 'No token found'], 401);
        }
    
        $accessToken = PersonalAccessToken::findToken($token);
    
        if ($accessToken) {
            $accessToken->delete();
        }
    
        return response()->json(['success' => 'Logged out successfully'])
            ->withoutCookie('auth_token');
    }
    function checkauth(Request $request)
    {
        $user = $request->user();
        return response()->json([
          "status" => "success",
          "user" => $user
        ]);
    }
}
