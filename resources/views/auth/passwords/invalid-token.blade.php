@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('title', 'Enlace Inválido')

@section('auth_header', 'Enlace Inválido')

@section('auth_body')
    <div class="text-center">
        <div class="mb-4">
            <i class="fas fa-exclamation-triangle fa-4x text-warning"></i>
        </div>
        <h4 class="text-danger mb-3">Este enlace ya no es válido</h4>
        <p class="text-muted mb-4">
            El enlace para restablecer la contraseña ha expirado o ya fue utilizado. 
            Por favor, solicita un nuevo enlace si necesitas restablecer tu contraseña.
        </p>
        <a href="{{ route('login') }}" class="btn btn-primary btn-block">
            <i class="fas fa-sign-in-alt mr-2"></i> Ir al Inicio de Sesión
        </a>
    </div>
@endsection
