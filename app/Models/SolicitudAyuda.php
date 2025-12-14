<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class SolicitudAyuda extends Model
{


    protected $table = 'solicitudes_ayuda';

    protected $fillable = [
        'voluntario_id',
        'tipo',
        'nivel_emergencia',
        'descripcion',
        'latitud',
        'longitud',
        'estado',
        'ci_voluntarios_acudir',
        'fecha_respondida',
        'ci_voluntario_accion',
    ];

    protected $casts = [
        'latitud'          => 'float',
        'longitud'         => 'float',
        'fecha_respondida' => 'datetime',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];

    public function voluntario()
    {
        return $this->belongsTo(User::class, 'voluntario_id', 'id_usuario');
    }

}
