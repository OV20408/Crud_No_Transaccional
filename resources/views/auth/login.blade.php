@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('auth_header', __('Iniciar sesión'))

@section('auth_body')
    <form action="{{ route('login') }}" method="post">
        @csrf

        {{-- CI --}}
        <div class="input-group mb-3">
            <input type="text" name="ci" class="form-control" placeholder="Carnet de Identidad" value="{{ old('ci') }}" required autofocus>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-id-card"></span></div>
            </div>
        </div>

        {{-- Contraseña --}}
        <div class="input-group mb-3">
            <input type="password" name="contrasena" class="form-control" placeholder="Contraseña" required>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-lock"></span></div>
            </div>
        </div>

        {{-- Botón de login --}}
        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-block">Ingresar</button>
            </div>
        </div>
    </form>
@endsection

@section('auth_footer')
    @section('auth_footer')
    <p class="my-0">
        <a href="{{ route('password.request') }}">
            {{ __('¿Olvidaste tu contraseña?') }}
        </a>
    </p>

    <p class="my-0">
        <a href="{{ route('register') }}">
            {{ __('¿No tienes cuenta? Regístrate aquí') }}
        </a>
    </p>
@endsection

@endsection
