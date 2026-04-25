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
Route::post('/login', [\App\Http\Controllers\FieldTrackAPI\UserController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    // Logout
    Route::post('/logout', [\App\Http\Controllers\FieldTrackAPI\UserController::class, 'logout']);

    // Dashboard
    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('/', [\App\Http\Controllers\AdminAPI\DashboardController::class, 'index']);
    });

    // Attendance
    Route::post('/punch-in', [\App\Http\Controllers\FieldTrackAPI\UserController::class, 'attendanceClockIn']);
    Route::post('/punch-out', [\App\Http\Controllers\FieldTrackAPI\UserController::class, 'attendanceClockOut']);
    Route::post('/attendance-status', [\App\Http\Controllers\FieldTrackAPI\UserController::class, 'attendanceStatus']);

    Route::post('today-attendance', [\App\Http\Controllers\FieldTrackAPI\UserController::class, 'todayAttendance']);
    Route::post('yesterday-status', [\App\Http\Controllers\FieldTrackAPI\UserController::class, 'yesterdayStatus']);
    Route::post('attendance-count-by-month', [\App\Http\Controllers\FieldTrackAPI\UserController::class, 'attendanceCountByMonth']);

    //Live Location Update (frequently by app side)
    Route::post('user-current-info', [\App\Http\Controllers\FieldTrackAPI\UserController::class, 'updateUserInfoFromApp'])->name('update-user-current-info');

    // Profile
    Route::get('/profile', [\App\Http\Controllers\FieldTrackAPI\UserController::class, 'myProfile']);

    // General API's
    Route::get('/get-countries', [\App\Http\Controllers\FieldTrackAPI\GeneralSettingsController::class, 'getCountries']);
    Route::get('/get-state-by-country/{countryId}', [\App\Http\Controllers\FieldTrackAPI\GeneralSettingsController::class, 'getStatesByCountry']);
    Route::get('/get-city-by-state/{stateId}', [\App\Http\Controllers\FieldTrackAPI\GeneralSettingsController::class, 'getCitiesByState']);
    Route::get('/get-area-by-city/{cityId}', [\App\Http\Controllers\FieldTrackAPI\GeneralSettingsController::class, 'getAreasByCity']);
    Route::get('/get-beat-by-area/{id}', [\App\Http\Controllers\FieldTrackAPI\GeneralSettingsController::class, 'getBeatByArea']);

    // Customer
    Route::resource('/customers', \App\Http\Controllers\FieldTrackAPI\CustomerController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy']);
    Route::post('/update-customer/{id}', [\App\Http\Controllers\FieldTrackAPI\CustomerController::class, 'update']);

    // Visit Management
    Route::group(['prefix' => 'visitors'], function () {
        Route::get('/', [\App\Http\Controllers\FieldTrackAPI\VisitorsController::class, 'index']);
        Route::post('/store', [\App\Http\Controllers\FieldTrackAPI\VisitorsController::class, 'store']);
        Route::get('/show/{id}', [\App\Http\Controllers\FieldTrackAPI\VisitorsController::class, 'show']);
        Route::post('/update/{id}', [\App\Http\Controllers\FieldTrackAPI\VisitorsController::class, 'update']);
        Route::delete('/delete/{id}', [\App\Http\Controllers\FieldTrackAPI\VisitorsController::class, 'destroy']);
    });
});
