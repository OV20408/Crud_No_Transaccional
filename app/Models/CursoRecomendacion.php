<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CursoRecomendacion extends Model
{
    use SoftDeletes;

    protected $table = 'curso_recomendaciones';
    
    protected $fillable = [
        'id_voluntario',
        'id_curso',
        'id_reporte',
        'mensaje_ia',
        'razon',
        'estado'
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
