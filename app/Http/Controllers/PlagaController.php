<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlagaController extends Controller
{
   
    public function mostrarFormulario()
    {
        return view('captura'); // sin .blade.php
    }


    public function guardarImagen(Request $request)
    {
        $imagen = $request->input('imagen');
        $imagen = str_replace('data:image/jpeg;base64,', '', $imagen);
        $imagen = str_replace(' ', '+', $imagen);
        $nombreImagen = time() . '.jpg';
        Storage::disk('public')->put($nombreImagen, base64_decode($imagen));

        return back()->with('success', 'Imagen guardada correctamente.');
    }
}

