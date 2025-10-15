<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth:api')->group(function () {
    Route::apiResource('tasks', TaskController::class);

    Route::prefix('exports')->group(function () {
        Route::post('/tasks', [ExportController::class, 'export']);
        Route::get('/tasks/{filename}/status', [ExportController::class, 'status']);
        Route::get('/tasks/{filename}/download', [ExportController::class, 'download']);
    });
});
