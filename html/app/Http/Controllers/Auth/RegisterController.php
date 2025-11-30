<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'nombres' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'ci' => ['required', 'string', 'max:255', 'unique:usuario,ci'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:usuario,email'],
            'contrasena' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'nombres' => $data['nombres'],
            'apellidos' => $data['apellidos'],
            'ci' => $data['ci'],
            'email' => $data['email'],
            'contrasena' => Hash::make($data['contrasena']),
            'estado' => 'activo',
            'id_rol' => 1, // por defecto "voluntario"
        ]);
    }

    /**
     * Sobrescribir el método register para forzar el uso de 'contrasena'
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        // Autenticar al usuario manualmente
        $this->guard()->login($user);

        return redirect($this->redirectPath());
    }
}
