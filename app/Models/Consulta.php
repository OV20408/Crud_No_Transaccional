<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    protected $table = 'consultas';

    protected $fillable = [
        'voluntario_id',
        'necesidad_id',
        'mensaje',
        'estado'
    ];

    public function voluntario()
    {
        return $this->belongsTo(\App\Models\User::class, 'voluntario_id', 'id_usuario');
    }


    public function necesidad()
    {
        return $this->belongsTo(\App\Models\Necesidad::class, 'necesidad_id');
    }
}
