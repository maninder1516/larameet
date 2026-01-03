<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// Controllers
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmployeeController;
    
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/profile', function () {
        return auth()->user();
    });
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::middleware('auth:api')->group(function () {
    Route::apiResource('employees', EmployeeController::class);
});

