<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="texto" class="form-label">{{ __('Texto de la pregunta') }} <span class="text-danger">*</span></label>
            <textarea name="texto" class="form-control @error('texto') is-invalid @enderror" id="texto" placeholder="Escriba el texto de la pregunta" rows="3" required minlength="5" maxlength="500">{{ old('texto', $pregunta?->texto) }}</textarea>
            {!! $errors->first('texto', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="tipo" class="form-label">{{ __('Tipo de respuesta') }} <span class="text-danger">*</span></label>
            <select name="tipo" id="tipo" class="form-control @error('tipo') is-invalid @enderror" required>
                <option value="">Seleccione el tipo</option>
                <option value="escala" {{ old('tipo', $pregunta?->tipo) == 'escala' ? 'selected' : '' }}>Escala (1-5)</option>
                <option value="texto" {{ old('tipo', $pregunta?->tipo) == 'texto' ? 'selected' : '' }}>Texto libre</option>
                <option value="si_no" {{ old('tipo', $pregunta?->tipo) == 'si_no' ? 'selected' : '' }}>Sí / No</option>
                <option value="multiple" {{ old('tipo', $pregunta?->tipo) == 'multiple' ? 'selected' : '' }}>Opción múltiple</option>
            </select>
            {!! $errors->first('tipo', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-3">
            <label for="id_test">{{ __('Test Asociado') }} <span class="text-danger">*</span></label>
            <select name="id_test" id="id_test" class="form-control @error('id_test') is-invalid @enderror" required>
                <option value="">Seleccione un test</option>
                @foreach ($tests as $test)
                    <option value="{{ $test->id }}"
                        {{ old('id_test', $pregunta->id_test ?? '') == $test->id ? 'selected' : '' }}>
                        {{ $test->nombre }}
                    </option>
                @endforeach
            </select>
            {!! $errors->first('id_test', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
    </div>
</div>