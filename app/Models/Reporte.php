<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Class Reporte
 *
 * @property $id
 * @property $estado_general
 * @property $fecha_generado
 * @property $observaciones
 * @property $recomendaciones
 * @property $resumen_emocional
 * @property $resumen_fisico
 *
 * @property Evaluacion[] $evaluaciones
 * @property ReporteCapacitacion[] $reporteCapacitaciones
 * @property ReporteNecesidad[] $reporteNecesidades
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Reporte extends Model
{


    protected $table = 'reporte';
    public $timestamps = false;
    protected $perPage = 20;

    /**
     * Campos asignables
     */
    protected $fillable = [
        'id_historial',
        'estado_general',
        'fecha_generado',
        'observaciones',
        'recomendaciones',
        'resumen_emocional',
        'resumen_fisico',
        'respuestas_fisico',
        'respuestas_emocional',
        'ci_voluntario_accion', // Trazabilidad API Gateway
    ];

    /**
     * Casts automáticos
     */
    protected $casts = [
        'fecha_generado' => 'datetime',
    ];

    /**
     * Relaciones
     */

    // 🔹 Un reporte puede tener varias evaluaciones
    public function evaluaciones()
    {
        return $this->hasMany(\App\Models\Evaluacion::class, 'id_reporte', 'id');
    }

    // 🔹 Relación con reporte_capacitacion
    public function reporteCapacitaciones()
    {
        return $this->hasMany(\App\Models\ReporteCapacitacion::class, 'id_reporte', 'id');
    }

    // 🔹 Relación con reporte_necesidad
    public function reporteNecesidades()
    {
        return $this->hasMany(\App\Models\ReporteNecesidad::class, 'id_reporte', 'id');
    }
}
