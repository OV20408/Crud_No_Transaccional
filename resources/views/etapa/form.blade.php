<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="nombre" class="form-label">{{ __('Nombre') }}</label>
            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $etapa?->nombre) }}" id="nombre" placeholder="Nombre">
            {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="orden" class="form-label">{{ __('Orden') }}</label>
            <input type="text" name="orden" class="form-control @error('orden') is-invalid @enderror" value="{{ old('orden', $etapa?->orden) }}" id="orden" placeholder="Orden">
            {!! $errors->first('orden', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="descripcion" class="form-label">{{ __('Descripción') }}</label>
            <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" placeholder="Descripción de la etapa" rows="3">{{ old('descripcion', $etapa?->descripcion) }}</textarea>
            {!! $errors->first('descripcion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-3">
            <label for="id_curso">{{ __('Curso') }}</label>
            <select name="id_curso" id="id_curso" class="form-control @error('id_curso') is-invalid @enderror" required>
                <option value="">Selecciona un curso</option>
                @foreach ($cursos as $curso)
                    <option value="{{ $curso->id }}"
                        {{ old('id_curso', $etapa->id_curso ?? '') == $curso->id ? 'selected' : '' }}>
                        {{ $curso->nombre }}
                    </option>
                @endforeach
            </select>
            {!! $errors->first('id_curso', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
    </div>
</div>