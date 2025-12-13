<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Consulta extends Model
{


    protected $table = 'consultas'; // tabla real
    public $timestamps = true;    // porque NO tiene updated_at

    protected $fillable = [
        'voluntario_id',
        'necesidad_id',
        'mensaje',
        'estado',
        'respuesta_admin',
        'ci_voluntario', // Trazabilidad API Gateway
    ];

    public function necesidad()
    {
        return $this->belongsTo(Necesidad::class, 'necesidad_id', 'id');
    }

    public function voluntario()
    {
        return $this->belongsTo(User::class, 'voluntario_id', 'id_usuario');
    }
}
