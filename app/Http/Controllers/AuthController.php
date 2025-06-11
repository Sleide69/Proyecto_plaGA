<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Mostrar formulario de registro
    public function showRegister() {
        return view('auth.register');
    }

    // Registrar un nuevo usuario
    public function register(Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'cedula' => 'required|string|max:20|unique:users,cedula',
            'password' => 'required|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'cedula' => $request->cedula,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login.form')->with('success', 'Registro exitoso. Inicia sesión.');
    }

    // Mostrar formulario de login
    public function showLogin() {
        return view('auth.login');
    }

    // Procesar login
    public function login(Request $request) {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // ✅ Generar token Sanctum y guardarlo en sesión
            $token = $user->createToken('plagas-token')->plainTextToken;
            session(['token_sanctum' => $token]);

            return redirect()->intended('/captura');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden.',
        ]);
    }

    // Cerrar sesión
    public function logout(Request $request) {
        $user = $request->user();

        // ✅ Eliminar todos los tokens de acceso del usuario (seguridad)
        if ($user) {
            $user->tokens()->delete();
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
