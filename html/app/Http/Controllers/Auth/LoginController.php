<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Redirección tras iniciar sesión
     */
    protected $redirectTo = '/home';

    /**
     * Crear instancia
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Login con CI
     */
    public function username()
    {
        return 'ci';
    }

    /**
     * Validación personalizada
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'ci' => 'required|string',
            'contrasena' => 'required|string',
        ]);
    }

    /**
     * Credenciales personalizadas
     */
    protected function credentials(Request $request)
    {
        return [
            'ci'       => $request->get('ci'),
            'password' => $request->get('contrasena'),
        ];
    }

    /**
     * DESPUÉS de un login exitoso, validamos el rol.
     */
    protected function authenticated(Request $request, $user)
    {
        // Solo admins pueden entrar a la web
        if ($user->rol?->nombre !== 'Administrador') {

            Auth::logout();

            return redirect()->back()->withErrors([
                'ci' => 'Solo los administradores pueden acceder al panel web.',
            ]);
        }

        // Si el usuario está inactivo
        if (strtolower($user->estado) !== 'activo') {

            Auth::logout();

            return redirect()->back()->withErrors([
                'ci' => 'Tu usuario está inactivo. Contacta al administrador.',
            ]);
        }
    }
}
