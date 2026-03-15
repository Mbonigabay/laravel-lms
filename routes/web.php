<?php

use Illuminate\Support\Facades\Route;
use Ka4ivan\LaravelLogger\Http\Controllers\LogViewerController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('logs', [LogViewerController::class, 'index'])->name('logs');
