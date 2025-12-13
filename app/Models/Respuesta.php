<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Class Respuestum
 *
 * @property $id
 * @property $respuesta_texto
 * @property $texto_pregunta
 * @property $id_evaluacion
 * @property $id_pregunta
 *
 * @property Evaluacion $evaluacion
 * @property Pregunta $pregunta
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Respuesta extends Model
{


    protected $table = 'respuesta';
    public $timestamps = false;
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'respuesta_texto',
        'texto_pregunta',
        'id_evaluacion',
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function evaluacion()
    {
        return $this->belongsTo(\App\Models\Evaluacion::class, 'id_evaluacion', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pregunta()
    {
        return $this->belongsTo(\App\Models\Pregunta::class, 'id_pregunta', 'id');
    }
    
}
