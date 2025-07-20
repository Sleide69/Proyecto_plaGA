<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class NuevaCapturaDetectada implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $imagenProcesada;
    public $detecciones;

    public function __construct($imagenProcesada, $detecciones)
    {
        $this->imagenProcesada = $imagenProcesada;
        $this->detecciones = $detecciones;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('capturas');
    }

    public function broadcastAs(): string
    {
        return 'NuevaCapturaDetectada';
    }
}