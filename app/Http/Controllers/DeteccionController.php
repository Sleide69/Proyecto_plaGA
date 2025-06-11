<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deteccion;

class DeteccionController extends Controller
{
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
