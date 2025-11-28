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
     * Definir el campo de login (CI en lugar de email)
     */
    public function username()
    {
        return 'ci';
    }

    /**
     * Validar los datos de login
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'ci' => 'required|string',
            'contrasena' => 'required|string',
        ]);
    }

    /**
     * Obtener credenciales personalizadas para el intento de login
     */
    protected function credentials(Request $request)
    {
        return [
            'ci' => $request->get('ci'),
            'password' => $request->get('contrasena'),
        ];
    }
}
