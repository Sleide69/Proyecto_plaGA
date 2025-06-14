<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class PlagaController extends Controller
{
    public function guardarImagen(Request $request)
    {
        $mensajeError = null;
        $vista = null;
        $detecciones = [];
        $imagenProcesada = '';

        $imagen = $request->input('imagen');

        // Validar que sea imagen base64
        if (!str_starts_with($imagen, 'data:image/jpeg;base64,')) {
            $mensajeError = 'Formato de imagen inválido.';
        } else {
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
                    $mensajeError = 'No se recibió respuesta del script de detección.';
                } else {
                    $detecciones = json_decode($output, true);

                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $mensajeError = 'La respuesta del detector no es un JSON válido.';
                    } elseif (!is_array($detecciones)) {
                        $mensajeError = 'El formato de las detecciones no es válido.';
                    } else {
                        $imagenProcesada = 'storage/' . $nombreImagen;
                        $vista = view('plagas.captura-imagen', compact('detecciones', 'imagenProcesada'));
                    }
                }
            } catch (\Exception $e) {
                $mensajeError = 'Error al ejecutar la detección: ' . $e->getMessage();
            }
        }

        // Retorno final
        if ($mensajeError) {
            return back()->with('error', $mensajeError);
        }

        return $vista;
    }

}
