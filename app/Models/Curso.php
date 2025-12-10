<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Curso
 *
 * @property int $id
 * @property string|null $descripcion
 * @property string $nombre
 * @property int $id_capacitacion
 *
 * @property \App\Models\Capacitacion $capacitacion
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Etapa[] $etapas
 */
class Curso extends Model
{
    use SoftDeletes;

    protected $table = 'curso';
    public $timestamps = false;

    protected $perPage = 20;

    protected $fillable = ['descripcion', 'nombre', 'id_capacitacion'];

    public function capacitacion()
    {
        return $this->belongsTo(\App\Models\Capacitacion::class, 'id_capacitacion', 'id');
    }


    public function etapas()
    {
        return $this->hasMany(\App\Models\Etapa::class, 'id_curso', 'id');
    }
}
