<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ChatMensaje extends Model
{


      protected $table = 'chat_mensajes';

    protected $fillable = [
        'voluntario_id',
        'de',
        'texto',
        'leido_en',
        'ci_voluntario_accion',
    ];

    public function voluntario()
    {
        // Tu modelo de usuario es App\Models\User con PK id_usuario
        return $this->belongsTo(User::class, 'voluntario_id', 'id_usuario');
    }
    //
}
