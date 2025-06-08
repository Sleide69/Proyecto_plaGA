<?php

use App\Http\Controllers\DeteccionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NotificacionController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/notificaciones', [NotificacionController::class, 'store']);
    Route::get('/notificaciones', [NotificacionController::class, 'index']);
});

Route::post('/notificar-plaga', [DeteccionController::class, 'notificar'])->middleware('auth:sanctum');
