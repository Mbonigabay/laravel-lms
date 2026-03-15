<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('logs', [\Ka4ivan\LaravelLogger\Http\Controllers\LogViewerController::class, 'index'])->name('logs');
