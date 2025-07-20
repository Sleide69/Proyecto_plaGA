<?php

namespace App\Http\Controllers\Docs;

/**
 * @OA\Get(
 *     path="/ws/usuario.{user_id}",
 *     summary="Canal WebSocket para notificaciones en tiempo real usando Laravel Reverb",
 *     description="
Conéctate al canal <b>ws://localhost:8080/usuario.{user_id}</b> para recibir notificaciones en tiempo real.<br>
<ul>
    <li>El parámetro <code>user_id</code> corresponde al ID del usuario autenticado.</li>
    <li>Usa <b>Laravel Echo</b> en el frontend para escuchar el evento <code>.NuevaNotificacion</code> en este canal.</li>
    <li>Ejemplo de conexión (con Javascript y Laravel Echo):<br>
    <pre>
window.Echo.channel('usuario.' + userId)
    .listen('.NuevaNotificacion', (e) => {
        console.log('Notificación:', e.notificacion);
    });
    </pre>
    </li>
</ul>
<b>Nota:</b> Swagger UI y Postman <u>no permiten probar WebSockets directamente</u>. Usa una herramienta como <a href='https://www.websocketking.com/' target='_blank'>WebSocket King Client</a>, Insomnia, o tu frontend para conectarte y escuchar eventos en tiempo real.
",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="ID del usuario autenticado"
 *     ),
 *     @OA\Response(
 *         response=101,
 *         description="Switching Protocols - Conexión WebSocket establecida"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Solicitud incorrecta (por ejemplo, falta user_id)"
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
class WebSocketController
{
    // Este controlador es solo para propósitos de documentación en Swagger/OpenAPI.
}