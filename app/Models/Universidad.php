<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Class Universidad
 *
 * @property $id
 * @property $direccion 
 * @property $nombre
 * @property $telefono
 *
 * @property Evaluacion[] $evaluacions
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Universidad extends Model
{


    protected $table = 'universidad';
        protected $primaryKey = 'id'; // Agregar esta línea

    public $timestamps = true;   // activar timestamps

    const CREATED_AT = 'created_at';  
    const UPDATED_AT = null;     // no existe updated_at en la tabla
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['direccion', 'nombre', 'telefono'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function evaluacions()
    {
        return $this->hasMany(\App\Models\Evaluacion::class, 'id', 'id_universidad');
    }
    
}
