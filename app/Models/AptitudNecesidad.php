<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AptitudNecesidad extends Model
{
    use SoftDeletes;

    protected $table = 'aptitud_necesidades';

    protected $fillable = [
        'id_voluntario',
        'id_necesidad',
        'id_reporte',
        'nivel_aptitud',
        'razon_ia',
        'necesidades_recomendadas',
        'estado'
    ];

    protected $casts = [
        'necesidades_recomendadas' => 'array',
    ];

    // Relación con Usuario (Voluntario)
    public function voluntario()
    {
        return $this->belongsTo(Usuario::class, 'id_voluntario', 'id_usuario');
    }

    // Relación con Necesidad
    public function necesidad()
    {
        return $this->belongsTo(Necesidad::class, 'id_necesidad', 'id');
    }

    // Relación con Reporte
    public function reporte()
    {
        return $this->belongsTo(Reporte::class, 'id_reporte', 'id');
    }
}
