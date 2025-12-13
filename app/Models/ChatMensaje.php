<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMensaje extends Model
{
    use SoftDeletes;

      protected $table = 'chat_mensajes';

    protected $fillable = [
        'voluntario_id',
        'de',
        'texto',
        'leido_en',
        'ci_voluntario', // Trazabilidad API Gateway
    ];

    public function voluntario()
    {
        // Tu modelo de usuario es App\Models\User con PK id_usuario
        return $this->belongsTo(User::class, 'voluntario_id', 'id_usuario');
    }
    //
}
