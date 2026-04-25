<?php

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
// Login
Route::post('/login', [\App\Http\Controllers\AdminAPI\AdminController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    // Logout
    Route::post('/logout', [\App\Http\Controllers\AdminAPI\AdminController::class, 'logout']);

    // Profile
    Route::get('/profile-info', [\App\Http\Controllers\AdminAPI\AdminController::class, 'profile']);

    // Dashboard
    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('/', [\App\Http\Controllers\AdminAPI\DashboardController::class, 'index']);
        Route::get('/productivity-breakdown', [\App\Http\Controllers\AdminAPI\DashboardController::class, 'productiveBreakdown']);
    });

    Route::get('/employees', [\App\Http\Controllers\AdminAPI\ScreenshotController::class, 'employees']);
    Route::get('/teams', [\App\Http\Controllers\AdminAPI\AppsAndUrlController::class, 'team']);
    Route::get('/employees-by-team', [\App\Http\Controllers\AdminAPI\AppsAndUrlController::class, 'EmployeeByTeam']);

    // screenshot
    Route::group(['prefix' => 'screenshot'], function () {
        Route::get('/view-screenshot', [\App\Http\Controllers\AdminAPI\ScreenshotController::class, 'viewScreenshot']);

        // Live
        Route::get('/live-available-employees-list', [\App\Http\Controllers\AdminAPI\ScreenshotController::class, 'liveScreenshotAvailableEmployees']);
        Route::post('/live-request-screenshot', [\App\Http\Controllers\AdminAPI\ScreenshotController::class, 'requestScreenshot']);
        Route::get('/get-live-screenshot-status', [\App\Http\Controllers\AdminAPI\ScreenshotController::class, 'checkScreenshotStatus']);
    });

    //App & Url
    Route::group(['prefix' => '/apps-and-url'], function () {
        Route::get('/', [\App\Http\Controllers\AdminAPI\AppsAndUrlController::class, 'appAndUrlUsage']);
    });
});
