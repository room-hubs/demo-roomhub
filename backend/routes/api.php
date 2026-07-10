<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use Illuminate\Support\Facades\Route;

// public route
Route::prefix('auth')->group(function () {

    // Credentials
    Route::post('/register',    [RegisterController::class, 'store'])
        ->middleware('throttle:10,1');

    Route::post('/login',       [LoginController::class, 'store'])
        ->middleware('throttle:10,1');

    Route::post('/admin/login', [LoginController::class, 'adminLogin'])
        ->middleware('throttle:5,1');

    Route::post('/refresh',     [LoginController::class, 'refresh'])
    ->middleware('throttle:10,1');

    // OAuth Google
    Route::get('/google/redirect', [LoginController::class, 'redirectGoogle']);
    Route::get('/google/callback', [LoginController::class, 'googleCallback']);

    //  OAuth Facebook
    Route::get('/facebook/redirect', [LoginController::class, 'redirectFacebook']);
    Route::get('/facebook/callback', [LoginController::class, 'facebookCallback']);

    // OAuth Code Exchange
    // Frontend POSTs the one-time code to get the real token
    Route::post('/oauth/exchange', [LoginController::class, 'exchangeOAuthCode'])
        ->middleware('throttle:10,1');

    // throttle: 5 requests per minute
    Route::middleware('throttle:5,1')->group(function () {
        // check by phone
        Route::post('/forgot-password/check-phone', [ForgotPasswordController::class, 'checkPhone']);
        Route::post('/forgot-password/verify-otp',  [ForgotPasswordController::class, 'verifyOtp']);
    });

    // reset-password is less frequently called; still protect it
    Route::post('/forgot-password/reset-password', [ForgotPasswordController::class, 'resetPassword'])
         ->middleware('throttle:10,1');
    Route::post('/forgot-password/reset-password-by-email', [ForgotPasswordController::class, 'resetPasswordByEmail'])
         ->middleware('throttle:10,1');

});

// protect route
Route::prefix('auth')->middleware('auth:sanctum')->group(function () {
    Route::get('/me',       [LoginController::class,   'me']);
    Route::post('/logout',  [LoginController::class,   'logout']);
    Route::post('/role',    [RegisterController::class, 'update']);
});
