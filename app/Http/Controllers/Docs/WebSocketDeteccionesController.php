<?php

namespace App\Http\Controllers\Docs;

/**
 * @OA\Get(
 *     path="/ws/detecciones",
 *     summary="Canal WebSocket global para detecciones en tiempo real usando Laravel Reverb",
 *     description="Conéctate al canal <b>ws://localhost:8080/detecciones</b> para recibir en tiempo real las nuevas detecciones reportadas por cualquier usuario.<br>
<ul>
    <li>Este canal es público para todos los usuarios autenticados.</li>
    <li>Usa <b>Laravel Echo</b> en el frontend para escuchar el evento <code>.NuevaDeteccion</code> en este canal.</li>
    <li>Ejemplo de conexión (con Javascript y Laravel Echo):<br>
    <pre>
window.Echo.channel('detecciones')
    .listen('.NuevaDeteccion', (e) => {
        console.log('Nueva detección:', e.deteccion);
    });
    </pre>
    </li>
</ul>
<b>Nota:</b> Swagger UI y Postman <u>no permiten probar WebSockets directamente</u>. Usa una herramienta como <a href='https://www.websocketking.com/' target='_blank'>WebSocket King Client</a>, Insomnia, o tu frontend para conectarte y escuchar eventos en tiempo real.",
 *     @OA\Response(
 *         response=101,
 *         description="Switching Protocols - Conexión WebSocket establecida"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="No autorizado para acceder a este canal"
 *     ),
 *     @OA\Server(
 *         url="ws://localhost:8080",
 *         description="Servidor WebSocket Reverb local"
 *     ),
 *     tags={"WebSocket"}
 * )
 */
class WebSocketDeteccionesController
{
    // Este controlador es solo para propósitos de documentación en Swagger/OpenAPI.
}