<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {


    Route::post('/student/{id}/documents', [DocumentController::class, 'index']);
    Route::post('/logout', [AuthController::class, 'logout']);

});
    Route::get('/students', [StudentController::class, 'index'])->middleware('auth:sanctum')->name('students');

Route::get('/refresh-token', [AuthController::class, 'refreshToken']);
Route::get('/login',[AuthController::class,'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/refresh-token', [AuthController::class, 'refreshToken']);


