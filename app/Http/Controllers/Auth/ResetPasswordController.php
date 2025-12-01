<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    
    protected $redirectTo = 'https://www.google.com';

    /**
     * Muestra el formulario de reset de contraseña.
     * Verifica si el token es válido antes de mostrar el formulario.
     */
    public function showResetForm(Request $request, $token = null)
    {
        $email = $request->email;
        
        // Verificar si el token es válido
        $user = \App\Models\User::where('email', $email)->first();
        
        if (!$user || !Password::broker()->tokenExists($user, $token)) {
            return view('auth.passwords.invalid-token');
        }
        
        return view('auth.passwords.reset')->with([
            'token' => $token,
            'email' => $email
        ]);
    }

    /**
     * Mensajes de respuesta en español.
     */
    protected function sendResetResponse(Request $request, $response)
    {
        return redirect($this->redirectPath())
            ->with('status', 'Tu contraseña ha sido restablecida correctamente.');
    }

    protected function sendResetFailedResponse(Request $request, $response)
    {
        if ($response === Password::INVALID_TOKEN) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Este enlace para restablecer la contraseña ha expirado o ya fue utilizado.']);
        }
        
        return redirect()->back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($response)]);
    }

    
    protected function resetPassword($user, $password)
    {
        // Usa tu mutator -> setPasswordAttribute()
        $user->password = $password;
        $user->setRememberToken(Str::random(60));
        $user->save();

        // IMPORTANTE: NO hacemos $this->guard()->login($user);
    }
}
