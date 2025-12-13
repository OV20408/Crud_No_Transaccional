<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

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
        'password',
    ];

    protected $hidden = [
        'contrasena',
        'remember_token',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function chatMensajes()
    {
        return $this->hasMany(\App\Models\ChatMensaje::class, 'voluntario_id', 'id_usuario');
    }

    public function solicitudesAyuda()
    {
        return $this->hasMany(\App\Models\SolicitudAyuda::class, 'voluntario_id', 'id_usuario');
    }
    
    public function toArray()
    {
        return array_filter(parent::toArray(), function ($value) {
            return !is_null($value);
        });
    }

    public function adminlte_desc()
    {
        return $this->nombres . ' ' . $this->apellidos;
    }

    public function adminlte_image()
    {
        // Opción 1: Usar Gravatar
        return 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($this->email))) . '?s=200&d=mp';
        
    }

    public function adminlte_profile_url()
    {
        return route('home'); // O la ruta de tu perfil
    }

    
    public function setPasswordAttribute($value)
    {
        if (strlen($value) < 60) {
            $this->attributes['contrasena'] = Hash::make($value);
        } else {
            $this->attributes['contrasena'] = $value;
        }
    }

    /**
     *  Getter virtual de password.
     *  Esto permite que Laravel trate "password" pero se lea "contrasena".
     */
    public function getPasswordAttribute()
    {
        return $this->contrasena;
    }

    /**
     *  Necesario para que el Auth de Laravel use "contrasena".
     */
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

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

    /**
     * Accessor para Helpdesk Widget - Primer nombre
     * Mapea 'nombres' de GEVOPI a 'first_name' esperado por el widget
     */
    public function getFirstNameAttribute()
    {
        if (!empty($this->nombres)) {
            // Si hay múltiples nombres, tomar solo el primero
            $nombres = explode(' ', trim($this->nombres));
            return $nombres[0] ?? '';
        }
        return '';
    }

    /**
     * Accessor para Helpdesk Widget - Apellido
     * Mapea 'apellidos' de GEVOPI a 'last_name' esperado por el widget
     */
    public function getLastNameAttribute()
    {
        return trim($this->apellidos ?? '');
    }
}
