<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Class ProgresoVoluntario
 *
 * @property $id
 * @property $estado
 * @property $fecha_finalizacion
 * @property $fecha_inicio
 * @property $id_etapa
 * @property $id_usuario
 *
 * @property Etapa $etapa
 * @property HistorialClinico $historialClinico
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class ProgresoVoluntario extends Model
{


    protected $table = 'progreso_voluntario';
    public $timestamps = false;
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['estado', 'fecha_finalizacion', 'fecha_inicio', 'id_etapa', 'id_usuario', 'ci_voluntario', 'ci_voluntario_accion'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function etapa()
    {
        return $this->belongsTo(\App\Models\Etapa::class, 'id_etapa', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function historialClinico()
    {
        return $this->belongsTo(\App\Models\HistorialClinico::class, 'id_usuario', 'id');
    }
    
}
