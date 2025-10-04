<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['jwt', 'premium'])->group(function () {
    Route::get('/shows', App\Http\Controllers\Shows\IndexController::class)->middleware('premium');
    Route::get('/shows/{show:slug}', App\Http\Controllers\Shows\ShowController::class)->middleware('premium');

    Route::get('/{show:slug}/episodes', [App\Http\Controllers\Episodes\IndexController::class, 'index']);
    Route::get('/episodes/{episode:slug}', [App\Http\Controllers\Episodes\ShowController::class, 'show']);
});
