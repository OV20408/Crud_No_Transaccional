<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class CursoRecomendacion extends Model
{


    protected $table = 'curso_recomendaciones';
    
    protected $fillable = [
        'id_voluntario',
        'id_curso',
        'id_reporte',
        'mensaje_ia',
        'razon',
        'estado',
        'ci_voluntario_accion', // Trazabilidad API Gateway
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function voluntario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_voluntario', 'id_usuario');
    }

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'id_curso');
    }

    public function reporte(): BelongsTo
    {
        return $this->belongsTo(Reporte::class, 'id_reporte');
    }
}
