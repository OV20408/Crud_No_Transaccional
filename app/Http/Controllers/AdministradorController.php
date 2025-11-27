<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

        $validated = $request->validate([
            'nombre'    => 'required|string|max:30',
            'apellido'  => 'required|string|max:30',
            'correo'    => 'required|email|max:50|unique:usuario,email',
            'ci'        => 'required|string|max:15|unique:usuario,ci',
            'extension' => 'nullable|string|max:5',
            'telefono'  => 'nullable|string|max:15',
        ]);

        $ciCompleto = $validated['ci'] .
            (!empty($validated['extension']) ? '-' . $validated['extension'] : '');

        User::create([
            'nombres'   => $validated['nombre'],
            'apellidos' => $validated['apellido'],
            'email'     => $validated['correo'],
            'ci'        => $ciCompleto,
            'telefono'  => $validated['telefono'] ?? null,
            'estado'    => 'activo',
            'id_rol'    => $idRolAdmin ?? 1,
            'contrasena'=> Hash::make('Admin123*'), // contraseña por defecto
        ]);

        return redirect()
            ->route('administradores.index')
            ->with('success', 'Administrador creado correctamente.');
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
