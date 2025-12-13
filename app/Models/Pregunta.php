<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Class Preguntum
 *
 * @property $id
 * @property $texto
 * @property $tipo
 * @property $id_test
 *
 * @property Test $test
 * @property Respuesta[] $respuestas
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Pregunta extends Model
{


    protected $table = 'pregunta';
    public $timestamps = false;
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['texto', 'tipo', 'id_test'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function test()
    {
        return $this->belongsTo(\App\Models\Test::class, 'id_test', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function respuestas()
    {
        return $this->hasMany(\App\Models\Respuesta::class, 'id_pregunta', 'id');
    }

    
}
