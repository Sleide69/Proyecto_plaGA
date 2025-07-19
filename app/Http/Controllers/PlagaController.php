<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class PlagaController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/plagas/detectar",
     *     summary="Detectar plagas en una imagen base64 usando YOLOv5",
     *     tags={"Plagas"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"imagen"},
     *             @OA\Property(
     *                 property="imagen",
     *                 type="string",
     *                 format="base64",
     *                 example="data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD..."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vista con imagen procesada y resultados de detección"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Formato de imagen inválido o error en la detección"
     *     )
     * )
     */
    public function guardarImagen(Request $request)
    {
        $mensajeError = null;
        $vista = null;
        $detecciones = [];
        $imagenProcesada = '';

        $imagen = $request->input('imagen');

        if (!str_starts_with($imagen, 'data:image/jpeg;base64,')) {
            $mensajeError = 'Formato de imagen inválido.';
        } else {
            $imagen = str_replace('data:image/jpeg;base64,', '', $imagen);
            $imagen = str_replace(' ', '+', $imagen);

            $nombreImagen = time() . '.jpg';
            $rutaLocal = storage_path('app/public/' . $nombreImagen);

            File::put($rutaLocal, base64_decode($imagen));

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

        if ($mensajeError) {
            return back()->with('error', $mensajeError);
        }

        return $vista;
    }

    public function mostrarFormulario()
    {
        return view('captura');
    }
}
