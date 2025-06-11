<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notificacion;

class NotificacionController extends Controller
{
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

    public function index(Request $request)
    {
        $user = $request->user(); // <- obtiene el usuario autenticado

        $notificaciones = Notificacion::where('user_id', $user->id)
                            ->latest()
                            ->take(10)
                            ->get();

        return response()->json(['notificaciones' => $notificaciones]);
    }

}
