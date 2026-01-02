<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SimpleLoggerController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/log', [SimpleLoggerController::class, 'logMessage']);
