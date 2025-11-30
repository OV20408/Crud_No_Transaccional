<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="nombre" class="form-label">{{ __('Nombre') }} <span class="text-danger">*</span></label>
            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $curso?->nombre) }}" id="nombre" placeholder="Nombre del curso" required minlength="3" maxlength="255">
            {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="descripcion" class="form-label">{{ __('Descripción') }}</label>
            <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" placeholder="Descripción del curso" rows="3" maxlength="500">{{ old('descripcion', $curso?->descripcion) }}</textarea>
            {!! $errors->first('descripcion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-3">
            <label for="id_capacitacion">Capacitación <span class="text-danger">*</span></label>
            <select name="id_capacitacion" id="id_capacitacion" class="form-control @error('id_capacitacion') is-invalid @enderror" required>
                <option value="">Seleccione una capacitación</option>
                @foreach ($capacitaciones as $capacitacion)
                    <option value="{{ $capacitacion->id }}"
                        {{ old('id_capacitacion', $curso->id_capacitacion ?? '') == $capacitacion->id ? 'selected' : '' }}>
                        {{ $capacitacion->nombre }}
                    </option>
                @endforeach
            </select>
            {!! $errors->first('id_capacitacion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>


    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
    </div>
</div>