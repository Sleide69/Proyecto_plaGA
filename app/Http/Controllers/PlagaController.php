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

        // Validar que sea imagen base64
        if (!str_starts_with($imagen, 'data:image/jpeg;base64,')) {
            return back()->with('error', 'Formato de imagen inválido.');
        }

        // Limpieza del base64
        $imagen = str_replace('data:image/jpeg;base64,', '', $imagen);
        $imagen = str_replace(' ', '+', $imagen);

        // Nombre y ruta
        $nombreImagen = time() . '.jpg';
        $rutaLocal = storage_path('app/public/' . $nombreImagen);

        // Guardar imagen
        File::put($rutaLocal, base64_decode($imagen));

        // Ejecutar YOLOv5
        try {
            $output = shell_exec("python3 scripts/detect_plaga.py " . escapeshellarg($rutaLocal));

            if (!$output) {
                return back()->with('error', 'No se recibió respuesta del script de detección.');
            }

            $detecciones = json_decode($output, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->with('error', 'La respuesta del detector no es un JSON válido.');
            }

            // Validar estructura del resultado
            if (!is_array($detecciones)) {
                return back()->with('error', 'El formato de las detecciones no es válido.');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Error al ejecutar la detección: ' . $e->getMessage());
        }

        // Construir ruta pública
        $imagenProcesada = 'storage/' . $nombreImagen;

        // Retornar vista con resultados
        return view('plagas.captura-imagen', compact('detecciones', 'imagenProcesada'));
    }
}
