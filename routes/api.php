<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\testController;
use App\Http\Middleware\RefreshTokenMiddleware;
use App\Http\Middleware\SetContentLanguageMiddleware;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test', [testController::class, 'mytest']);


//auth
Route::post('/otp/send',   [AuthController::class, 'sendOtp']);
Route::post('/otp/verify',   [AuthController::class, 'verifyOtp']);
Route::post('/register',   [AuthController::class, 'register']);
Route::post('/login',   [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/logout', [AuthController::class, 'logout']);

    Route::prefix('reports')->group(function () {
        Route::post('/', [ReportController::class, 'store']); 
        Route::get('/', [ReportController::class, 'index']); 
        Route::get('/{id}', [ReportController::class, 'show']); 
    });
});

// Route::middleware(SetContentLanguageMiddleware::class)->group(function () {
//     Route::post('/posts', [PostController::class, 'show']);
// });


//for the refresh token request
Route::middleware(['auth:sanctum', RefreshTokenMiddleware::class, SetContentLanguageMiddleware::class])->group(function () {
    Route::prefix('posts')->group(function () {
        Route::post('/normal', [PostController::class, 'showNormalPosts']); 
        Route::get('/admin', [PostController::class, 'showAdminPosts']); 
    });
});






 