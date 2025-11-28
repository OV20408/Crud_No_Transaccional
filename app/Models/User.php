<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash; // 👈 NUEVO

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';
    public $timestamps = true;

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

    protected $hidden = [
        'contrasena',
        'password', // 👈 por si alguien accede a este atributo virtual
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Ocultar campos nulos en JSON
    public function toArray()
    {
        return array_filter(parent::toArray(), function ($value) {
            return !is_null($value);
        });
    }

    // 👇 Esto hace que cualquier $user->password = '...' se guarde en "contrasena"
    public function setPasswordAttribute($value)
    {
        $this->attributes['contrasena'] = Hash::make($value);
    }
 
    // Mutador para contrasena - hashea automáticamente
    public function setContrasenaAttribute($value)
    {
        $this->attributes['contrasena'] = Hash::make($value);
    }

    public function getPasswordAttribute()
    {
        return $this->contrasena;
    }

    public function getAuthPassword()
    {
        return $this->contrasena;
    }


    //verificar later
    /* public function setPasswordAttribute($value)
    {
        $this->attributes['contrasena'] = $value;
    } */

    

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id');
    }

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
