<?php


use App\Http\Controllers\LandingPageApi\BlogApiController;
use App\Http\Controllers\LandingPageApi\ContactUsApiController;

use App\Http\Controllers\Web\LandingPage\PageController;


Route::prefix('api')->name('landing.api')->group(function () {

    Route::get('blog/categories', [BlogApiController::class, 'categories']);
    Route::get('blog/category/{slug}', [BlogApiController::class, 'categoryDetails']);
    Route::get('/category/{category_slug}/blog/{blog_slug}', [BlogApiController::class, 'blogDetails']);


    Route::post('/contact-us', [ContactUsApiController::class, 'contactUs']);

});
