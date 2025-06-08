<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $fillable = ['user_id', 'mensaje', 'leido'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
