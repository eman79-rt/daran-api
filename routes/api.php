<?php

use App\Http\Controllers\Api\V1\HomeController as V1HomeController;
use App\Http\Controllers\Api\V1\PropertyController as V1PropertyController;
use App\Http\Controllers\Api\V2\HomeController as V2HomeController;
use App\Http\Controllers\Api\V2\PropertyController as V2PropertyController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/properties', [V1PropertyController::class, 'index']);
    Route::get('/properties/{property}', [V1PropertyController::class, 'show']);
    Route::get('/home', V1HomeController::class);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/properties', [V1PropertyController::class, 'store']);
        Route::put('/properties/{property}', [V1PropertyController::class, 'update']);
        Route::delete('/properties/{property}', [V1PropertyController::class, 'destroy']);
    });
});
Route::prefix('v2')->group(function () {
    Route::get('/properties', [V2PropertyController::class, 'index']);
    Route::get('/properties/{property}', [V2PropertyController::class, 'show']);
    Route::get('/home', V2HomeController::class);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/properties', [V2PropertyController::class, 'store']);
        Route::put('/properties/{property}', [V2PropertyController::class, 'update']);
        Route::delete('/properties/{property}', [V2PropertyController::class, 'destroy']);
    });
});
