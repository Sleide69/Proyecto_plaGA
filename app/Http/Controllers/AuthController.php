<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class AuthController extends Controller
{
    public function showRegister() {
        return view('auth.register');
    }

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

    public function showLogin() {
        return view('auth.login');
    }

    public function login(Request $request) {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // ✅ Generar token Sanctum para JS/Frontend
            $token = $user->createToken('plagas-token')->plainTextToken;
            session(['token_sanctum' => $token]); // Guardar en sesión

            return redirect()->intended('/captura');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden.',
        ]);
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
