<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;


class PlagaController extends Controller
{

    public function mostrarFormulario()
    {
        return view('captura');
    }


    public function guardarImagen(Request $request)
    {
        $imagen = $request->input('imagen');

        // Limpieza del base64
        $imagen = str_replace('data:image/jpeg;base64,', '', $imagen);
        $imagen = str_replace(' ', '+', $imagen);

        // Nombre y ruta de almacenamiento
        $nombreImagen = time() . '.jpg';
        $rutaLocal = storage_path('app/public/' . $nombreImagen);

        // Guardar imagen
        File::put($rutaLocal, base64_decode($imagen));

        // Ejecutar script YOLOv5
        $output = shell_exec("python3 scripts/detect_plaga.py " . escapeshellarg($rutaLocal));

        // Decodificar JSON
        $detecciones = json_decode($output, true);

        // Construir ruta pública accesible
        $imagenProcesada = 'storage/' . $nombreImagen;

        // Retornar vista con datos
        return view('plagas.captura-imagen', compact('detecciones', 'imagenProcesada'));
    }

}
