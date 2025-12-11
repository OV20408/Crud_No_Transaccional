<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="tipo" class="form-label">{{ __('Tipo de necesidad') }} <span class="text-danger">*</span></label>
            <select name="tipo" id="tipo" class="form-control @error('tipo') is-invalid @enderror" required>
                <option value="">Seleccione el tipo</option>
                <option value="Fisica" {{ old('tipo', $necesidad?->tipo) == 'Fisica' ? 'selected' : '' }}>Física</option>
                <option value="Psicologica" {{ old('tipo', $necesidad?->tipo) == 'Psicologica' ? 'selected' : '' }}>Psicológica</option>
                <option value="Formacion" {{ old('tipo', $necesidad?->tipo) == 'Formacion' ? 'selected' : '' }}>Formación</option>
                <option value="Recursos" {{ old('tipo', $necesidad?->tipo) == 'Recursos' ? 'selected' : '' }}>Recursos</option>
                <option value="Especializado" {{ old('tipo', $necesidad?->tipo) == 'Especializado' ? 'selected' : '' }}>Especializado</option>
                <option value="Otro" {{ old('tipo', $necesidad?->tipo) == 'Otro' ? 'selected' : '' }}>Otro</option>
            </select>
            {!! $errors->first('tipo', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="descripcion" class="form-label">{{ __('Descripción') }} <span class="text-danger">*</span></label>
            <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" placeholder="Describa la necesidad detectada" rows="3" required minlength="10" maxlength="500">{{ old('descripcion', $necesidad?->descripcion) }}</textarea>
            {!! $errors->first('descripcion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
    </div>
</div>