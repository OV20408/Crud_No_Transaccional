<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="nombre" class="form-label">{{ __('Nombre') }} <span class="text-danger">*</span></label>
            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $test?->nombre) }}" id="nombre" placeholder="Nombre del test" required minlength="3" maxlength="255">
            {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="categoria" class="form-label">{{ __('Categoría') }} <span class="text-danger">*</span></label>
            <select name="categoria" id="categoria" class="form-control @error('categoria') is-invalid @enderror" required>
                <option value="">Seleccione una categoría</option>
                <option value="Fisico" {{ old('categoria', $test?->categoria) == 'Fisico' ? 'selected' : '' }}>Físico</option>
                <option value="Psicologico" {{ old('categoria', $test?->categoria) == 'Psicologico' ? 'selected' : '' }}>Psicológico</option>
                <option value="Emocional" {{ old('categoria', $test?->categoria) == 'Emocional' ? 'selected' : '' }}>Emocional</option>
                <option value="General" {{ old('categoria', $test?->categoria) == 'General' ? 'selected' : '' }}>General</option>
            </select>
            {!! $errors->first('categoria', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="descripcion" class="form-label">{{ __('Descripción') }}</label>
            <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" placeholder="Descripción del test" rows="3" maxlength="500">{{ old('descripcion', $test?->descripcion) }}</textarea>
            {!! $errors->first('descripcion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
    </div>
</div>