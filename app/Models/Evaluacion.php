<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Class Evaluacion
 *
 * @property $id
 * @property $fecha
 * @property $id_reporte
 * @property $id_test
 * @property $id_universidad
 *
 * @property Reporte $reporte
 * @property Test $test
 * @property Universidad $universidad
 * @property Respuesta[] $respuestas
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Evaluacion extends Model
{


    protected $table = 'evaluacion';
    public $timestamps = false;
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['fecha', 'id_reporte', 'id_test', 'id_universidad', 'ci_voluntario_accion'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reporte()
    {
        return $this->belongsTo(\App\Models\Reporte::class, 'id_reporte', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function test()
    {
        return $this->belongsTo(\App\Models\Test::class, 'id_test', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function universidad()
    {
        return $this->belongsTo(\App\Models\Universidad::class, 'id_universidad', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function respuestas()
    {
        return $this->hasMany(\App\Models\Respuesta::class, 'id', 'id_evaluacion');
    }
    
}
