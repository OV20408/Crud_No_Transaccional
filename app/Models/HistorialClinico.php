<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class HistorialClinico
 *
 * @property $id
 * @property $email
 * @property $fecha_actualizacion
 * @property $fecha_inicio
 *
 * @property ProgresoVoluntario[] $progresoVoluntarios
 * @property Reporte[] $reportes
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class HistorialClinico extends Model
{
    protected $table = 'historial_clinico';
    public $timestamps = false;
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['email', 'fecha_actualizacion', 'fecha_inicio'];
    protected $casts = [
        'fecha_actualizacion' => 'datetime',
        'fecha_inicio' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function progresoVoluntarios()
    {
        return $this->hasMany(\App\Models\ProgresoVoluntario::class, 'id', 'id_usuario');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reportes()
    {
        return $this->hasMany(\App\Models\Reporte::class, 'id', 'id_historial');
    }
    
}
