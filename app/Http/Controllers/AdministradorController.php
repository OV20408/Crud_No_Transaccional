<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password; // 👈 IMPORTANTE
use Illuminate\Support\Str;              // 👈 IMPORTANTE

class AdministradorController extends Controller
{
    public function index(Request $request)
    {
        $idRolAdmin = Rol::where('nombre', 'Administrador')->value('id');

        $admins = User::with('rol')
            ->when($idRolAdmin, fn($q) => $q->where('id_rol', $idRolAdmin))
            ->orderBy('nombres')
            ->orderBy('apellidos')
            ->get();

        return view('administradores.index', compact('admins'));
    }

    public function create()
    {
        return view('administradores.create');
    }

    public function store(Request $request)
    {
        $idRolAdmin = Rol::where('nombre', 'Administrador')->value('id');

        // 🧾 Validación usando los NOMBRES de campo de tu form Blade
        $validated = $request->validate([
            'nombre'    => 'required|string|max:30',
            'apellido'  => 'required|string|max:30',
            'correo'    => 'required|email|max:50|unique:usuario,email',
            'ci'        => 'required|string|max:15|unique:usuario,ci',
            'extension' => 'nullable|string|max:5',
            'telefono'  => 'nullable|string|max:15',
        ]);

        // CI + extensión (opcional)
        $ciCompleto = $validated['ci'] .
            (!empty($validated['extension']) ? '-' . $validated['extension'] : '');

        // 1️⃣ Crear el usuario con una contraseña ALEATORIA que él no conoce
        $user = User::create([
            'nombres'   => $validated['nombre'],
            'apellidos' => $validated['apellido'],
            'email'     => $validated['correo'],
            'ci'        => $ciCompleto,
            'telefono'  => $validated['telefono'] ?? null,
            'estado'    => 'activo',
            'id_rol'    => $idRolAdmin ?? 1,
            // Algo random y fuerte, solo para tener un hash en la BD
            'contrasena'=> Hash::make(Str::random(32)),
        ]);

        // 2️⃣ Enviar el link de "reset password" para que él ponga su propia clave
        Password::broker()->sendResetLink([
            'email' => $user->email,
        ]);

        return redirect()
            ->route('administradores.index')
            ->with(
                'success',
                'Administrador creado. Se envió un correo para que configure su contraseña.'
            );
    }

    public function toggleEstado($id)
    {
        $admin = User::findOrFail($id);

        $admin->estado = strtolower($admin->estado) === 'activo' ? 'inactivo' : 'activo';
        $admin->save();

        return redirect()
            ->route('administradores.index')
            ->with('success', 'Estado actualizado correctamente.');
    }
}


