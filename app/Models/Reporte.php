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
 * @property $id_historial
 *
 * @property HistorialClinico $historialClinico
 * @property Evaluacion[] $evaluacions
 * @property ReporteCapacitacion[] $reporteCapacitacions
 * @property ReporteNecesidad[] $reporteNecesidads
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Reporte extends Model
{
    protected $table = 'reporte';
    public $timestamps = false;
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['estado_general', 'fecha_generado', 'observaciones', 'recomendaciones', 'resumen_emocional', 'resumen_fisico', 'id_historial'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function historialClinico()
    {
        return $this->belongsTo(\App\Models\HistorialClinico::class, 'id_historial', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function evaluacions()
    {
        return $this->hasMany(\App\Models\Evaluacion::class, 'id', 'id_reporte');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reporteCapacitacions()
    {
        return $this->hasMany(\App\Models\ReporteCapacitacion::class, 'id', 'id_reporte');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reporteNecesidads()
    {
        return $this->hasMany(\App\Models\ReporteNecesidad::class, 'id', 'id_reporte');
    }
    
}
