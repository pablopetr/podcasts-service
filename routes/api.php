<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('jwt:podcasts')->group(function () {
    Route::get('/episodes', [App\Http\Controllers\EpisodeController::class, 'index']);
    Route::get('/episodes/{slug}', [App\Http\Controllers\EpisodeController::class, 'show']);
});
