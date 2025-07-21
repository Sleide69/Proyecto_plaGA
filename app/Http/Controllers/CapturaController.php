<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
// Opcional: para broadcasting en tiempo real
// use App\Events\NuevaCapturaDetectada;

class CapturaController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/captura",
     *     summary="Guardar imagen base64 y detectar plagas",
     *     description="Recibe una imagen en formato base64, la guarda en el servidor, la envía a un microservicio para detección de plagas y retorna la vista con la imagen procesada y los resultados.",
     *     tags={"Captura"},
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
     *         description="No se recibió ninguna imagen"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al comunicarse con el detector de plagas"
     *     )
     * )
     */
    public function guardarImagen(Request $request)
    {
        $dataUri = $request->input('imagen');
        if (!$dataUri) {
            return response()->json(['error' => 'No se recibió ninguna imagen.'], 400);
        }

        $image = str_replace('data:image/jpeg;base64,', '', $dataUri);
        $image = str_replace(' ', '+', $image);
        $imageData = base64_decode($image);

        $nombreArchivo = 'capturas/' . Str::uuid() . '.jpg';
        Storage::disk('public')->put($nombreArchivo, $imageData);
        $rutaPublica = 'storage/' . $nombreArchivo;

        try {
            $response = Http::attach(
                'image',
                file_get_contents(storage_path('app/public/' . $nombreArchivo)),
                'captura.jpg'
            )->post('http://script:5000/detect');

            $detecciones = $response->json();

            $deteccionesFiltradas = [];
            foreach ($detecciones as $det) {
                if (isset($det['name'], $det['confidence'])) {
                    $deteccionesFiltradas[] = $det;
                }
            }

            // Opcional: Emitir evento de broadcasting con los resultados
            // event(new NuevaCapturaDetectada($rutaPublica, $deteccionesFiltradas));

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al comunicarse con el detector de plagas: ' . $e->getMessage()], 500);
        }

        // Devuelve una vista (para API REST sería mejor devolver JSON, pero así está en tu ejemplo)
        return view('plagas.captura-imagen', [
            'imagenProcesada' => $rutaPublica,
            'detecciones' => $deteccionesFiltradas,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/captura/formulario",
     *     summary="Mostrar formulario para subir una imagen",
     *     tags={"Captura"},
     *     @OA\Response(
     *         response=200,
     *         description="Formulario HTML para subir imagen"
     *     )
     * )
     */
    public function mostrarFormulario()
    {
        return view('captura');
    }
}