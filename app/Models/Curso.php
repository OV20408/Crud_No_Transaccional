<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Curso
 *
 * @property $id
 * @property $descripcion
 * @property $nombre
 * @property $id_capacitacion
 *
 * @property Capacitacion $capacitacion
 * @property Etapa[] $etapas
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Curso extends Model
{
    protected $table = 'curso';
    public $timestamps = false;
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['descripcion', 'nombre', 'id_capacitacion'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function capacitacion()
    {
        return $this->belongsTo(\App\Models\Capacitacion::class, 'id_capacitacion', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function etapas()
    {
        return $this->hasMany(\App\Models\Etapa::class, 'id', 'id_curso');
    }
    
}
