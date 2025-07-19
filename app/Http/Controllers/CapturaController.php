<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class CapturaController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/captura",
     *     summary="Guardar imagen base64 y detectar plagas",
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
            return back()->with('error', 'No se recibió ninguna imagen.');
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
            )->post('http://127.0.0.1:5000/detect');

            $detecciones = $response->json();

            $deteccionesFiltradas = [];
            foreach ($detecciones as $det) {
                if (isset($det['name'], $det['confidence'])) {
                    $deteccionesFiltradas[] = $det;
                }
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Error al comunicarse con el detector de plagas: ' . $e->getMessage());
        }

        return view('plagas.captura-imagen', [
            'imagenProcesada' => $rutaPublica,
            'detecciones' => $deteccionesFiltradas,
        ]);
    }

    public function mostrarFormulario()
    {
        return view('captura');
    }
}
