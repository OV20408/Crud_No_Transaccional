<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        {{-- Usuario asociado --}}
        <div class="form-group mb-2 mb20">
            <label for="id_usuario" class="form-label">{{ __('Usuario') }}</label>
            <select name="id_usuario" id="id_usuario" class="form-control @error('id_usuario') is-invalid @enderror" required>
                <option value="">-- Seleccionar usuario --</option>
                @foreach(\App\Models\User::all() as $usuario)
                    <option value="{{ $usuario->id_usuario }}"
                        {{ old('id_usuario', $historialClinico?->id_usuario) == $usuario->id_usuario ? 'selected' : '' }}>
                        {{ $usuario->nombres }} {{ $usuario->apellidos }} ({{ $usuario->ci }})
                    </option>
                @endforeach
            </select>
            {!! $errors->first('id_usuario', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        {{-- Fecha de inicio --}}
        <div class="form-group mb-2 mb20">
            <label for="fecha_inicio" class="form-label">{{ __('Fecha de Inicio') }}</label>
            <input
                type="datetime-local"
                name="fecha_inicio"
                id="fecha_inicio"
                class="form-control @error('fecha_inicio') is-invalid @enderror"
                value="{{ old('fecha_inicio', $historialClinico?->fecha_inicio ? $historialClinico->fecha_inicio->format('Y-m-d\TH:i') : '') }}"
            >
            {!! $errors->first('fecha_inicio', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        {{-- Fecha de actualización --}}
        <div class="form-group mb-2 mb20">
            <label for="fecha_actualizacion" class="form-label">{{ __('Fecha de Actualización') }}</label>
            <input
                type="datetime-local"
                name="fecha_actualizacion"
                id="fecha_actualizacion"
                class="form-control @error('fecha_actualizacion') is-invalid @enderror"
                value="{{ old('fecha_actualizacion', $historialClinico?->fecha_actualizacion ? $historialClinico->fecha_actualizacion->format('Y-m-d\TH:i') : '') }}"
            >
            {!! $errors->first('fecha_actualizacion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>

    {{-- Botón de envío --}}
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
    </div>
</div>
