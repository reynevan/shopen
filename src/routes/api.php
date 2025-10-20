<?php

use Illuminate\Support\Facades\Route;
use Shopen\Http\Controllers\Frontend\Api\BannerTrackController;
use Shopen\Http\Controllers\Frontend\Api\SearchController;

Route::middleware('api')->prefix('api')->group(function () {
    Route::get('szukaj', [SearchController::class, 'search']);
    Route::post('/banners/track/{banner}', BannerTrackController::class)->name('banners.track');
});