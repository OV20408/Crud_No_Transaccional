@extends('adminlte::auth.auth-page', ['auth_type' => 'register'])

@section('auth_header', __('Registro de Usuario'))

@section('auth_body')
    <form action="{{ route('register') }}" method="post">
        @csrf

        {{-- Nombres --}}
        <div class="input-group mb-3">
            <input type="text" name="nombres" class="form-control" placeholder="Nombres" value="{{ old('nombres') }}" required autofocus>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-user"></span></div>
            </div>
        </div>

        {{-- Apellidos --}}
        <div class="input-group mb-3">
            <input type="text" name="apellidos" class="form-control" placeholder="Apellidos" value="{{ old('apellidos') }}" required>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-user-tag"></span></div>
            </div>
        </div>

        {{-- CI --}}
        <div class="input-group mb-3">
            <input type="text" name="ci" class="form-control" placeholder="Carnet de Identidad" value="{{ old('ci') }}" required>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-id-card"></span></div>
            </div>
        </div>

        {{-- Correo --}}
        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control" placeholder="Correo electrónico" value="{{ old('email') }}" required>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-envelope"></span></div>
            </div>
        </div>

        {{-- Contraseña --}}
        <div class="input-group mb-3">
            <input type="password" name="contrasena" class="form-control" placeholder="Contraseña" required>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-lock"></span></div>
            </div>
        </div>

        {{-- Confirmación --}}
        <div class="input-group mb-3">
            <input type="password" name="contrasena_confirmation" class="form-control" placeholder="Confirmar contraseña" required>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-lock"></span></div>
            </div>
        </div>

        {{-- Botón --}}
        <button type="submit" class="btn btn-primary btn-block">Registrar</button>
    </form>
@endsection

@section('auth_footer')
    <p class="my-0">
        <a href="{{ route('login') }}">
            {{ __('Ya tengo una cuenta') }}
        </a>
    </p>
@endsection
