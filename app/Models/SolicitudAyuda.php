<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SolicitudAyuda extends Model
{
    use SoftDeletes;

    protected $table = 'solicitudes_ayuda';

    protected $fillable = [
        'voluntario_id',
        'tipo',              // ← Campo en DB
        'tipo_emergencia',   // ✅ AGREGAR ESTE
        'nivel_emergencia',
        'descripcion',
        'latitud',
        'longitud',
        'estado',
        'ci_voluntarios_acudir',
        'fecha_respondida',
        'ci_voluntario_accion', // Trazabilidad API Gateway
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
