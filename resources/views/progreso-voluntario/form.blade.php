<div class="row padding-1 p-1">
    <div class="col-md-12">

        {{-- ESTADO --}}
        <div class="form-group mb-2 mb20">
            <label for="estado" class="form-label">{{ __('Estado') }}</label>
            <input type="text" name="estado"
                class="form-control @error('estado') is-invalid @enderror"
                value="{{ old('estado', $progresoVoluntario?->estado) }}" id="estado"
                placeholder="Ej: En progreso, Completado, Pendiente">
            {!! $errors->first('estado', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
        </div>

        {{-- FECHA INICIO --}}
        <div class="form-group mb-2 mb20">
            <label for="fecha_inicio" class="form-label">{{ __('Fecha de Inicio') }}</label>
            <input type="datetime-local" name="fecha_inicio"
                class="form-control @error('fecha_inicio') is-invalid @enderror"
                value="{{ old('fecha_inicio', isset($progresoVoluntario->fecha_inicio) ? \Carbon\Carbon::parse($progresoVoluntario->fecha_inicio)->format('Y-m-d\TH:i') : '') }}"
                id="fecha_inicio">
            {!! $errors->first('fecha_inicio', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
        </div>

        {{-- FECHA FINALIZACION --}}
        <div class="form-group mb-2 mb20">
            <label for="fecha_finalizacion" class="form-label">{{ __('Fecha de Finalización') }}</label>
            <input type="datetime-local" name="fecha_finalizacion"
                class="form-control @error('fecha_finalizacion') is-invalid @enderror"
                value="{{ old('fecha_finalizacion', isset($progresoVoluntario->fecha_finalizacion) ? \Carbon\Carbon::parse($progresoVoluntario->fecha_finalizacion)->format('Y-m-d\TH:i') : '') }}"
                id="fecha_finalizacion">
            {!! $errors->first('fecha_finalizacion', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
        </div>

        {{-- ETAPA --}}
        <div class="form-group mb-2 mb20">
            <label for="id_etapa" class="form-label">{{ __('Etapa Asociada') }}</label>
            <select name="id_etapa" id="id_etapa"
                class="form-control @error('id_etapa') is-invalid @enderror" required>
                <option value="">Seleccione una etapa</option>

                @foreach ($etapas as $etapa)
                    <option value="{{ $etapa->id }}"
                        {{ old('id_etapa', $progresoVoluntario->id_etapa ?? '') == $etapa->id ? 'selected' : '' }}>
                        {{ $etapa->id }} - {{ $etapa->nombre ?? 'Sin nombre' }}
                    </option>
                @endforeach
            </select>
            {!! $errors->first('id_etapa', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
        </div>

        {{-- USUARIO REAL --}}
        <div class="form-group mb-2 mb20">
            <label for="id_usuario" class="form-label">{{ __('Usuario Asociado') }}</label>
            <select name="id_usuario" id="id_usuario"
                class="form-control @error('id_usuario') is-invalid @enderror" required>

                <option value="">Seleccione un usuario</option>

                @foreach ($usuarios as $usuario)
                    <option value="{{ $usuario->id_usuario }}"
                        {{ old('id_usuario', $progresoVoluntario->id_usuario ?? '') == $usuario->id_usuario ? 'selected' : '' }}>
                        {{ $usuario->id_usuario }} -
                        {{ $usuario->nombres }} {{ $usuario->apellidos }}
                        (CI: {{ $usuario->ci }})
                    </option>
                @endforeach

            </select>
            {!! $errors->first('id_usuario', '<div class="invalid-feedback"><strong>:message</strong></div>') !!}
        </div>

    </div>

    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
    </div>
</div>
