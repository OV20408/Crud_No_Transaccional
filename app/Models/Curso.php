<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    protected $table = 'curso';
    public $timestamps = false;

    protected $perPage = 20;

    protected $fillable = ['descripcion', 'nombre', 'id_capacitacion'];

    /**
     * Relación: curso pertenece a una capacitación
     */
    public function capacitacion()
    {
        return $this->belongsTo(\App\Models\Capacitacion::class, 'id_capacitacion', 'id');
    }

    /**
     * Relación: curso tiene muchas etapas
     */
    public function etapas()
    {
        // 🔴 Antes lo tenías al revés: hasMany(Etapa::class, 'id', 'id_curso')
        // ✅ Debe ser:
        // FK en tabla etapa: id_curso → PK en curso: id
        return $this->hasMany(\App\Models\Etapa::class, 'id_curso', 'id');
    }
}
