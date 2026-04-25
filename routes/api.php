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
Route::post('login', [\App\Http\Controllers\DesktopAPI\UserController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum', 'detect_plan_expire_And_delete_media', VerifyAccessTokenForDeskTopApk::class]], function () {


    // Profile
    Route::get('my-profile', [\App\Http\Controllers\DesktopAPI\UserController::class, 'myProfile']);

    // Logout
    Route::post('logout', [\App\Http\Controllers\DesktopAPI\UserController::class, 'logout']);

    // Attendance
    Route::post('punch-in', [\App\Http\Controllers\DesktopAPI\EmployeeAttendanceController::class, 'attendanceClockIn']);
    Route::post('punch-out', [\App\Http\Controllers\DesktopAPI\EmployeeAttendanceController::class, 'attendanceClockOut']);
    Route::post('attendance-status', [\App\Http\Controllers\DesktopAPI\EmployeeAttendanceController::class, 'attendanceStatus']);

    // Idle Time
    Route::post('idle-time-start', [\App\Http\Controllers\DesktopAPI\EmployeeAttendanceController::class, 'idleTimeStart']);
    Route::put('idle-time-end/{id}', [\App\Http\Controllers\DesktopAPI\EmployeeAttendanceController::class, 'idleTimEnd']);

    // Break
    Route::get('available-break', [\App\Http\Controllers\DesktopAPI\BreakController::class, 'getAvailableBreaks']);
    Route::get('break', [\App\Http\Controllers\DesktopAPI\BreakController::class, 'breaks']);
    Route::post('start-break', [\App\Http\Controllers\DesktopAPI\BreakController::class, 'startBreak']);
    Route::post('end-break', [\App\Http\Controllers\DesktopAPI\BreakController::class, 'endBreak']);

    // Event
    Route::group(['prefix' => 'event'], function () {
        Route::post('/routine', [\App\Http\Controllers\DesktopAPI\EventControlle::class, 'routineEvent']);
        Route::post('/live-stream', [\App\Http\Controllers\Web\Company\LivestreamController::class, 'store']);
    });
});
