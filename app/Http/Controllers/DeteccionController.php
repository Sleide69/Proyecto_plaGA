<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deteccion;
use App\Events\NuevaDeteccion;

class DeteccionController extends Controller
{
    /**
 * @OA\Post(
 *     path="/api/detecciones",
 *     summary="Registrar una detección de plaga",
 *     description="Registra una nueva detección de plaga asociada al usuario autenticado. Este endpoint requiere autenticación (Sanctum).",
 *     tags={"Detecciones"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"plaga", "ubicacion", "hora"},
 *             @OA\Property(property="plaga", type="string", example="Mosca blanca"),
 *             @OA\Property(property="ubicacion", type="string", example="Invernadero 3, sector norte"),
 *             @OA\Property(property="hora", type="string", format="date-time", example="2025-07-19T14:30:00Z")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Detección registrada exitosamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="mensaje", type="string", example="Detectada y guardada"),
 *             @OA\Property(property="id", type="integer", example=42)
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="No autenticado"
 *     )
 * )
 */
    public function notificar(Request $request)
    {
        $user = $request->user();

        $det = Deteccion::create([
            'user_id' => $user->id,
            'plaga' => $request->plaga,
            'ubicacion' => $request->ubicacion,
            'hora_detectada' => $request->hora,
        ]);

        return response()->json(['mensaje' => 'Detectada y guardada', 'id' => $det->id], 201);
    }
}