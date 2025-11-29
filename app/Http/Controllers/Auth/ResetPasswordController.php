<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    
    protected $redirectTo = '/login';

    
    protected function resetPassword($user, $password)
    {
        // Usa tu mutator -> setPasswordAttribute()
        $user->password = $password;
        $user->setRememberToken(Str::random(60));
        $user->save();

        // IMPORTANTE: NO hacemos $this->guard()->login($user);
    }
}
