<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NuevaNotificacion implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $notificacion;

    /**
     * NuevaNotificacion constructor.
     * 
     * @param  \App\Models\Notificacion  $notificacion
     */
    public function __construct($notificacion)
    {
        $this->notificacion = $notificacion;
    }

    /**
     * Canal donde se emite el evento.
     */
    public function broadcastOn(): Channel
    {
        // Canal único por usuario
        return new Channel('usuario.' . $this->notificacion->user_id);
    }

    /**
     * Nombre del evento en el frontend.
     */
    public function broadcastAs(): string
    {
        return 'NuevaNotificacion';
    }
}