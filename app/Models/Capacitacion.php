<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Capacitacion
 *
 * @property $id
 * @property $descripcion
 * @property $nombre
 *
 * @property Curso[] $cursos
 * @property Reporte[] $reporteCapacitacions
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Capacitacion extends Model
{
    use SoftDeletes;

    protected $table = 'capacitacion';
    public $timestamps = false;
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['descripcion', 'nombre'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cursos()
    {
        return $this->hasMany(\App\Models\Curso::class, 'id_capacitacion', 'id');
    }

    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    
    
}
