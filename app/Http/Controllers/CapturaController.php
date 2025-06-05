<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class CapturaController extends Controller
{
    public function guardarImagen(Request $request)
    {
        // Verifica si se recibió la imagen base64
        $dataUri = $request->input('imagen');
        if (!$dataUri) {
            return back()->with('error', 'No se recibió ninguna imagen.');
        }

        // Decodifica la imagen base64
        $image = str_replace('data:image/jpeg;base64,', '', $dataUri);
        $image = str_replace(' ', '+', $image);
        $imageData = base64_decode($image);

        // Guarda la imagen en storage/app/public/capturas
        $nombreArchivo = 'capturas/' . Str::uuid() . '.jpg';
        Storage::disk('public')->put($nombreArchivo, $imageData);
        $rutaPublica = 'storage/' . $nombreArchivo;


        // (Opcional) Guardar en BD si tienes tabla capturas
                // \DB::table('capturas')->insert([
                //     'imagen' => $rutaPublica,
                //     'resultado' => json_encode($detecciones),
                //     'created_at' => now(),
                //     'updated_at' => now(),
                // ]);


        // Envía la imagen al detector Flask
        try {
            $response = Http::attach(
                'image',
                file_get_contents(storage_path('app/public/' . $nombreArchivo)),
                'captura.jpg'
            )->post('http://127.0.0.1:5000/detect');

            $detecciones = $response->json();

            // Filtra las detecciones para evitar errores
            $deteccionesFiltradas = [];
            foreach ($detecciones as $det) {
                if (isset($det['name'], $det['confidence'])) {
                    $deteccionesFiltradas[] = $det;
                }
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Error al comunicarse con el detector de plagas: ' . $e->getMessage());
        }

        // Retorna la vista con resultados
        return view('plagas.captura-imagen', [
            'imagenProcesada' => $rutaPublica,
            'detecciones' => $deteccionesFiltradas,
        ]);
    }
}

