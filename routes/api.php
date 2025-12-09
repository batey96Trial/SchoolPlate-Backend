<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;


Route::prefix("v1")->group(function(){
    Route::controller(AuthController::class)->group(function () {
    Route::get('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/refresh-token','refreshToken');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

    Route::middleware('auth:sanctum')->group(function(){
        Route::apiResource('students',StudentController::class);
        Route::apiResource('donors',DonorController::class);
    });

});