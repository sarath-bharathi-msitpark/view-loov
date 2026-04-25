<?php

use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

Route::prefix('field-track')->middleware(['auth', 'plan'])->name('fieldTrack.')->group(function () {

    // Dashboard
    Route::group(['middleware' => ['role_or_permission:administrator']], function () {
        Route::get('/dashboard', [\App\Http\Controllers\Web\FieldTrack\DashboardController::class, 'index'])->name('dashboard');
    });

    //Live Location
    Route::group(['middleware' => ['role_or_permission:administrator']], function () {
        Route::get('/live-location', [\App\Http\Controllers\Web\FieldTrack\LiveLocationController::class, 'index'])->name('live_location');
        Route::get('/getLiveLocation', [\App\Http\Controllers\Web\FieldTrack\LiveLocationController::class, 'getLiveLocation'])->name('getLiveLocation');
    });

    Route::group(['middleware' => ['role_or_permission:administrator']], function () {
        Route::get('/attendanceemployee', [\App\Http\Controllers\Web\FieldTrack\AttendanceEmployeeController::class, 'index'])->name('attendanceemployee.index');
        Route::get('/export/attendanceemployee', [\App\Http\Controllers\Web\FieldTrack\AttendanceEmployeeController::class, 'export'])->name('attendanceemployee.export');
    });

    // Customer Management
    Route::group(['prefix' => 'customers', 'as' => 'customer.', 'middleware' => ['role_or_permission:administrator']], function () {
        Route::get('/', [\App\Http\Controllers\Web\FieldTrack\CustomerController::class, 'index'])->name('index');
        Route::get('/view-customer/{id}', [\App\Http\Controllers\Web\FieldTrack\CustomerController::class, 'show'])->name('show');
        Route::delete('/remove-customer/{id}', [\App\Http\Controllers\Web\FieldTrack\CustomerController::class, 'destroy'])->name('delete');
    });

    // Visit Management
    Route::group(['prefix' => 'visits', 'as' => 'visits.', 'middleware' => ['role_or_permission:administrator']], function () {
        Route::get('/', [\App\Http\Controllers\Web\FieldTrack\VisitsController::class, 'index'])->name('index');
    });

    // Location Country
    Route::group(['prefix' => 'location', 'as' => 'location.', 'middleware' => ['role_or_permission:administrator']], function () {

        // Country
        Route::get('countries', [\App\Http\Controllers\Web\FieldTrack\LocationController::class, 'countryIndex'])->name('countries.index');
        Route::post('countries', [\App\Http\Controllers\Web\FieldTrack\LocationController::class, 'countryStore'])->name('countries.store');
        Route::put('countries/{country}', [\App\Http\Controllers\Web\FieldTrack\LocationController::class, 'countryUpdate'])->name('countries.update');
        Route::delete('countries/{country}', [\App\Http\Controllers\Web\FieldTrack\LocationController::class, 'countryDestroy'])->name('countries.destroy');
        Route::patch('countries/{country}/toggle-status', [\App\Http\Controllers\Web\FieldTrack\LocationController::class, 'countryToggleStatus'])->name('countries.toggleStatus');

        // State
        Route::get('states', [\App\Http\Controllers\Web\FieldTrack\LocationController::class, 'stateIndex'])->name('states.index');
        Route::post('states', [\App\Http\Controllers\Web\FieldTrack\LocationController::class, 'stateStore'])->name('states.store');
        Route::put('states/{state}', [\App\Http\Controllers\Web\FieldTrack\LocationController::class, 'stateUpdate'])->name('states.update');
        Route::delete('states/{state}', [\App\Http\Controllers\Web\FieldTrack\LocationController::class, 'stateDestroy'])->name('states.destroy');
        Route::patch('states/{state}/toggle-status', [\App\Http\Controllers\Web\FieldTrack\LocationController::class, 'stateToggleStatus'])->name('states.toggleStatus');

        // City
        Route::get('cities', [\App\Http\Controllers\Web\FieldTrack\LocationController::class, 'cityIndex'])->name('cities.index');
        Route::post('cities', [\App\Http\Controllers\Web\FieldTrack\LocationController::class, 'cityStore'])->name('cities.store');
        Route::put('cities/{city}', [\App\Http\Controllers\Web\FieldTrack\LocationController::class, 'cityUpdate'])->name('cities.update');
        Route::delete('cities/{city}', [\App\Http\Controllers\Web\FieldTrack\LocationController::class, 'cityDestroy'])->name('cities.destroy');
        Route::patch('cities/{city}/toggle-status', [\App\Http\Controllers\Web\FieldTrack\LocationController::class, 'cityToggleStatus'])->name('cities.toggleStatus');

        // Area
        Route::get('areas', [\App\Http\Controllers\Web\FieldTrack\LocationController::class, 'areaIndex'])->name('areas.index');
        Route::post('areas', [\App\Http\Controllers\Web\FieldTrack\LocationController::class, 'areaStore'])->name('areas.store');
        Route::put('areas/{area}', [\App\Http\Controllers\Web\FieldTrack\LocationController::class, 'areaUpdate'])->name('areas.update');
        Route::delete('areas/{area}', [\App\Http\Controllers\Web\FieldTrack\LocationController::class, 'areaDestroy'])->name('areas.destroy');
        Route::patch('areas/{area}/toggle-status', [\App\Http\Controllers\Web\FieldTrack\LocationController::class, 'areaToggleStatus'])->name('areas.toggleStatus');

        // Beat
        Route::get('beats', [\App\Http\Controllers\Web\FieldTrack\LocationController::class, 'beatIndex'])->name('beats.index');
        Route::post('beats', [\App\Http\Controllers\Web\FieldTrack\LocationController::class, 'beatStore'])->name('beats.store');
        Route::put('beats/{beat}', [\App\Http\Controllers\Web\FieldTrack\LocationController::class, 'beatUpdate'])->name('beats.update');
        Route::delete('beats/{beat}', [\App\Http\Controllers\Web\FieldTrack\LocationController::class, 'beatDestroy'])->name('beats.destroy');
        Route::patch('beats/{beat}/toggle-status', [\App\Http\Controllers\Web\FieldTrack\LocationController::class, 'beatToggleStatus'])->name('beats.toggleStatus');

        // ajax dependent dropdown
        Route::get('get-states/{country}', [\App\Http\Controllers\Web\FieldTrack\LocationController::class, 'getStates'])->name('getStates');
        Route::get('get-cities/{state}', [\App\Http\Controllers\Web\FieldTrack\LocationController::class, 'getCities'])->name('getCities');
        Route::get('get-areas/{city}', [\App\Http\Controllers\Web\FieldTrack\LocationController::class, 'getAreas'])->name('getAreas');

    });
});
