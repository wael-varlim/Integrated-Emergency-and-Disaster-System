<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\testController;
use App\Http\Middleware\RefreshTokenMiddleware;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test', [testController::class, 'mytest']);



Route::post('/sendotp',   [AuthController::class, 'sendOtp']);
Route::post('/verifyotp',   [AuthController::class, 'verifyOtp']);

Route::post('/register',   [AuthController::class, 'register']);
Route::post('/login',   [AuthController::class, 'login']);
// Route::post('/refresh', [AuthController::class, 'refresh']);

Route::middleware(['auth:sanctum', RefreshTokenMiddleware::class])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});
