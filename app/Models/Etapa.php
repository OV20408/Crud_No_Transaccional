<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Class Etapa
 *
 * @property int $id
 * @property string $nombre
 * @property int $orden
 * @property int $id_curso
 *
 * @property \App\Models\Curso $curso
 */
class Etapa extends Model
{


    protected $table = 'etapa';
    public $timestamps = false;

    protected $perPage = 20;

    protected $fillable = ['nombre', 'orden', 'id_curso', 'descripcion'];
    /**
     * Relación: etapa pertenece a un curso
     */
    public function curso()
    {
        return $this->belongsTo(\App\Models\Curso::class, 'id_curso', 'id');
    }

    /**
     * Relación: etapa tiene muchos progresos
     */
    public function progresoVoluntarios()
    {
        // FK en progreso_voluntario: id_etapa → PK en etapa: id
        return $this->hasMany(\App\Models\ProgresoVoluntario::class, 'id_etapa', 'id');
    }
}
