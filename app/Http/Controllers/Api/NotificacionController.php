<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notificacion;
use App\Events\NuevaNotificacion;

class NotificacionController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/notificaciones",
     *     summary="Crear una nueva notificación y emitirla en tiempo real",
     *     tags={"Notificaciones"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"mensaje"},
     *             @OA\Property(property="mensaje", type="string", maxLength=255, example="Tienes una nueva alerta")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notificación creada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="notificacion", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=5),
     *                 @OA\Property(property="mensaje", type="string", example="Tienes una nueva alerta"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-19T20:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-19T20:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function store(Request $request) {
        $user = $request->user(); // autenticado por Sanctum

        $request->validate([
            'mensaje' => 'required|string|max:255',
        ]);

        $noti = Notificacion::create([
            'user_id' => $user->id,
            'mensaje' => $request->mensaje,
        ]);

        return response()->json(['success' => true, 'notificacion' => $noti]);
    }

    /**
     * @OA\Get(
     *     path="/api/notificaciones",
     *     summary="Obtener las últimas 10 notificaciones del usuario autenticado",
     *     tags={"Notificaciones"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de notificaciones",
     *         @OA\JsonContent(
     *             @OA\Property(property="notificaciones", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="user_id", type="integer", example=5),
     *                     @OA\Property(property="mensaje", type="string", example="Tienes una nueva alerta"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-07-19T20:00:00Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-07-19T20:00:00Z")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $user = $request->user(); // obtiene el usuario autenticado

        $notificaciones = Notificacion::where('user_id', $user->id)
                            ->latest()
                            ->take(10)
                            ->get();

        return response()->json(['notificaciones' => $notificaciones]);
    }
}