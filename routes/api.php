<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['jwt', 'premium'])->group(function () {
    Route::get('/shows', App\Http\Controllers\Shows\IndexController::class)->middleware('premium');

    Route::get('/{show:slug}/episodes', [App\Http\Controllers\EpisodeController::class, 'index']);
    Route::get('/episodes/{episode:slug}', [App\Http\Controllers\EpisodeController::class, 'show']);
});
