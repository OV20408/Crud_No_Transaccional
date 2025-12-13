<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Class Test
 *
 * @property $id
 * @property $categoria
 * @property $descripcion
 * @property $nombre
 *
 * @property Evaluacion[] $evaluacions
 * @property Pregunta[] $preguntas
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Test extends Model
{


    protected $table = 'test';
    public $timestamps = false;

    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['categoria', 'descripcion', 'nombre'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function evaluacions()
    {
        return $this->hasMany(\App\Models\Evaluacion::class, 'id', 'id_test');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function preguntas()
    {
        return $this->hasMany(\App\Models\Pregunta::class, 'id', 'id_test');
    }
    
}
