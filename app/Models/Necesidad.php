<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Class Necesidad
 *
 * @property $id
 * @property $descripcion
 * @property $tipo
 *
 * @property ReporteNecesidad[] $reporteNecesidads
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Necesidad extends Model
{


    protected $table = 'necesidad';
    public $timestamps = true;

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['descripcion', 'tipo', 'created_at', 'updated_at'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reporteNecesidads()
    {
        return $this->hasMany(\App\Models\ReporteNecesidad::class, 'id', 'id_necesidad');
    }
    
}
