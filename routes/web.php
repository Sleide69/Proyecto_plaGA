<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlagaController;
use App\Http\Controllers\AuthController;

// Página de bienvenida
Route::get('/', function () {
    return view('welcome');
});

// Registro
Route::get('/register', [AuthController::class, 'showRegister'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');

// Login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Capturar imagen (protegido)
Route::get('/capturar-imagen', [PlagaController::class, 'mostrarFormulario'])
    ->middleware('auth')
    ->name('captura.imagen');

Route::post('/guardar-imagen', [PlagaController::class, 'guardarImagen'])
    ->middleware('auth')
    ->name('guardar.imagen');



