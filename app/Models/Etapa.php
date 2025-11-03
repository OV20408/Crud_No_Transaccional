<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Etapa
 *
 * @property $id
 * @property $nombre
 * @property $orden
 * @property $id_curso
 *
 * @property Curso $curso
 * @property ProgresoVoluntario[] $progresoVoluntarios
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Etapa extends Model
{
    protected $table = 'etapa';
    public $timestamps = false;
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['nombre', 'orden', 'id_curso'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function curso()
    {
        return $this->belongsTo(\App\Models\Curso::class, 'id_curso', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function progresoVoluntarios()
    {
        return $this->hasMany(\App\Models\ProgresoVoluntario::class, 'id', 'id_etapa');
    }
    
}
