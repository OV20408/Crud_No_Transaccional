<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class HistorialClinico extends Model
{


    protected $table = 'historial_clinico';
    public $timestamps = false;
    protected $perPage = 20;

    protected $fillable = [
        'id_usuario',
        'fecha_inicio',
        'fecha_actualizacion',
        'ci_voluntario_accion',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_actualizacion' => 'datetime',
    ];

    // Relación con usuario
    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_usuario', 'id_usuario');
    }

    // Relación con progreso voluntario
    public function progresoVoluntarios()
    {
        return $this->hasMany(\App\Models\ProgresoVoluntario::class, 'id_usuario', 'id_usuario');
    }

    // (En la nueva BD, reporte ya no tiene id_historial, así que quitamos esa relación)
}
