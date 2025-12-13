@extends('adminlte::auth.auth-page', ['auth_type' => 'register'])

@section('auth_header', __('Registro de Usuario'))

@section('auth_body')
    <form action="{{ route('register') }}" method="post">
        @csrf

        {{-- Nombres --}}
        <div class="input-group mb-3">
            <input type="text" id="nombres" name="nombres" class="form-control" placeholder="Nombres" value="{{ old('nombres') }}" required autofocus>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-user"></span></div>
            </div>
        </div>

        {{-- Apellidos --}}
        <div class="input-group mb-3">
            <input type="text" id="apellidos" name="apellidos" class="form-control" placeholder="Apellidos" value="{{ old('apellidos') }}" required>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-user-tag"></span></div>
            </div>
        </div>

        {{-- CI --}}
        <div class="input-group mb-3">
            <input type="text" id="ci" name="ci" class="form-control" placeholder="Carnet de Identidad" value="{{ old('ci') }}" required>
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

    @php
        $gatewayLookupUrl = rtrim(env('GATEWAY_REGISTRO_SIMPLE_URL', ''), '/');
    @endphp

    @if($gatewayLookupUrl)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ciInput       = document.getElementById('ci');
                const nombreInput   = document.getElementById('nombres');
                const apellidoInput = document.getElementById('apellidos');
                const telefonoInput = document.getElementById('telefono'); // opcional en el formulario

                const lookupBaseUrl = @json($gatewayLookupUrl);
                const clientSystem  = @json(env('SYSTEM_NAME', 'GEVOPI'));

                if (!ciInput || !lookupBaseUrl) {
                    return;
                }

                let lastLookupCi = null;
                let isFetching   = false;

                ciInput.addEventListener('blur', async function () {
                    const ci = (ciInput.value || '').trim();

                    // Evitar llamadas con CI muy corto o repetidas
                    if (ci.length < 5 || ci === lastLookupCi || isFetching) {
                        return;
                    }

                    lastLookupCi = ci;
                    isFetching   = true;

                    try {
                        const url = `${lookupBaseUrl}/${encodeURIComponent(ci)}`;

                        const response = await fetch(url, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-Client-System': clientSystem,
                            },
                        });

                        if (!response.ok) {
                            console.warn('Gateway lookup failed with status', response.status);
                            return;
                        }

                        const json = await response.json();

                        // Si el gateway no encontró nada, no tocamos el formulario
                        if (!json.success || !json.found || !json.data) {
                            return;
                        }

                        const data = json.data;

                        // Solo rellenar campos vacíos para no pisar lo que el usuario ya escribió
                        if (nombreInput && !nombreInput.value.trim() && (data.nombre || data.nombres)) {
                            nombreInput.value = data.nombre ?? data.nombres ?? '';
                        }
                        if (apellidoInput && !apellidoInput.value.trim() && (data.apellido || data.apellidos)) {
                            apellidoInput.value = data.apellido ?? data.apellidos ?? '';
                        }
                        if (telefonoInput && !telefonoInput.value.trim() && data.telefono) {
                            telefonoInput.value = data.telefono;
                        }

                        // Normalizar el CI si viene formateado distinto
                        if (data.ci && ciInput.value.trim() !== data.ci) {
                            ciInput.value = data.ci;
                        }

                    } catch (error) {
                        console.error('Error llamando al gateway para autocompletar', error);
                    } finally {
                        isFetching = false;
                    }
                });
            });
        </script>
    @endif
@endsection
