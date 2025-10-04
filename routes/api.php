<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['jwt:podcasts', 'premium'])->group(function () {
    Route::get('/shows', App\Http\Controllers\Shows\IndexController::class)->middleware('premium');
    Route::get('/shows/{show:slug}', App\Http\Controllers\Shows\ShowController::class)->middleware('premium');

    Route::get('/{show:slug}/episodes', [App\Http\Controllers\Episodes\IndexController::class, 'index']);
    Route::get('/episodes/{episode:slug}', [App\Http\Controllers\Episodes\ShowController::class, 'show']);
});

Route::prefix('/admin')
    ->middleware(['jwt:podcasts', 'requireScope:admin,podcasts:write'])
    ->group(function () {
        Route::get('/shows', App\Http\Controllers\Admin\Shows\IndexController::class);
        Route::get('/shows/{show:slug}', App\Http\Controllers\Admin\Shows\ShowController::class);
        Route::post('/shows', App\Http\Controllers\Admin\Shows\StoreController::class);
        Route::put('/shows/{show:slug}', App\Http\Controllers\Admin\Shows\UpdateController::class);
        Route::delete('/shows/{show:slug}', App\Http\Controllers\Admin\Shows\DestroyController::class);
    });
