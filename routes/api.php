<?php

use App\Events\MyEvent;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\SendNotificationController;
use App\Http\Controllers\SocketController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});



Route::post('/post',[SendNotificationController::class,'store']);
// Route::get('/test-broadcast', function () {
//     \Log::info('Testing broadcast');
//     event(new MyEvent("Test message from direct route"));
//     return "Event test triggered";
// });


Route::prefix('auth')->group(function () {
    Route::post('signup', [AuthController::class, 'register']);
    Route::post('signin', [AuthController::class, 'login']);
    Route::post('signout', [AuthController::class, 'logout'])->middleware('check.token');
    Route::get('check', [AuthController::class, 'checkauth'])->middleware('check.token');
    Route::get('/user/connect/{userId}', [SocketController::class, 'onUserConnect']);
    Route::get('/user/disconnect/{userId}', [SocketController::class, 'onUserDisconnect']);
});

Route::prefix('message')->group(function () {
    Route::get('/{id}', [MessageController::class, 'getMessages'])->middleware('check.token');
    Route::post('/{receiver_id}', [MessageController::class, 'sendMessage'])->middleware('check.token');
});

Route::prefix('user')->group(function () {
    Route::get('/', [UserController::class, 'getUsersForSidebar'])->middleware('check.token');
    Route::put('/update-profile', [UserController::class, 'updateProfile'])->middleware('check.token');
});