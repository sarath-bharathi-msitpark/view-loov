<?php

use App\Http\Middleware\VerifyAccessTokenForDeskTopApk;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/create-stealth-user', [App\Http\Controllers\StealthAPI\AuthController::class, 'CreateStealthUser']);

// Profile
Route::get('/stealth-profile', [App\Http\Controllers\StealthAPI\AuthController::class, 'stealthProfile']);

Route::group(['middleware' => ['auth:sanctum', VerifyAccessTokenForDeskTopApk::class]], function () {
    // Logout
    Route::post('/logout', [App\Http\Controllers\StealthAPI\AuthController::class, 'logout']);

    // Event
    Route::group(['prefix' => 'event'], function () {
        Route::post('/routine', [\App\Http\Controllers\StealthAPI\EventController::class, 'routineEvent']);
        Route::post('/live-stream', [\App\Http\Controllers\Web\Company\LivestreamController::class, 'store']);
    });
});
