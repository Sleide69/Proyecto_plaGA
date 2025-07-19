<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlagaController;
use App\Http\Controllers\CapturaController;

// Página de bienvenida
Route::get('/', function () {
    return view('welcome');
});

// ----------------------------
// 🔐 Autenticación
// ----------------------------

Route::get('/register', [AuthController::class, 'showRegister'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ----------------------------
// 📸 Formulario de captura de imagen
// ----------------------------

Route::get('/captura', [PlagaController::class, 'mostrarFormulario'])
    ->middleware('auth')
    ->name('formulario.captura');
    
Route::get('/captura-imagen', [CapturaController::class, 'mostrarFormulario']);

// ----------------------------
// 💾 Guardar imagen y procesar
// ----------------------------

Route::post('/captura-imagen', [CapturaController::class, 'guardarImagen'])
    ->middleware('auth')
    ->name('captura.imagen');



// ----------------------------
// 📊 Registrar detecciones (opcional desde IA externa)
// ----------------------------

Route::post('/plagas-detectadas', function (Request $request) {
    \App\Models\Captura::create([
        'plaga_detectada' => $request->plaga,
        'confianza' => $request->confianza,
        'solucion' => $request->solucion,
        'fecha_captura' => $request->capturado_en,
    ]);

    return response()->json(['mensaje' => 'Plaga registrada por IA']);
}
);
