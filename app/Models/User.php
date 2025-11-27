<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // 🔹 Nombre real de la tabla
    protected $table = 'usuario';

    // 🔹 Clave primaria personalizada
    protected $primaryKey = 'id_usuario';

    // 🔹 No usar 'id' auto para timestamps de Laravel
    public $timestamps = true;

    // 🔹 Campos asignables
    protected $fillable = [
        'nombres',
        'apellidos',
        'ci',
        'fecha_nacimiento',
        'genero',
        'telefono',
        'email',
        'direccion_domicilio',
        'contrasena',
        'estado',
        'id_rol',
        'nivel_entrenamiento',
        'entidad_pertenencia',
        'tipo_sangre',
        'foto_ci',
        'licencia_conducir',
        'foto_licencia',
    ];

    // 🔹 Ocultos al serializar
    protected $hidden = [
        'contrasena',
    ];

    // 🔹 Casteos automáticos
    protected $casts = [
        'fecha_nacimiento' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // 🔹 IMPORTANTE: indicar a Laravel cuál campo es el password
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    // 🔹 Relación con Rol
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id');
    }

    // 🚀 NUEVO: iniciales para el avatar (ej. "Omar Velasco" -> "OV")
    public function getInicialesAttribute()
    {
        $n = trim($this->nombres ?? '');
        $a = trim($this->apellidos ?? '');

        $ini = '';

        if ($n !== '') {
            $ini .= mb_substr($n, 0, 1, 'UTF-8');
        }
        if ($a !== '') {
            $ini .= mb_substr($a, 0, 1, 'UTF-8');
        }

        return mb_strtoupper($ini, 'UTF-8');
    }
}
