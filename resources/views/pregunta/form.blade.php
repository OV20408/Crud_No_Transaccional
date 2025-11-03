<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="texto" class="form-label">{{ __('Texto') }}</label>
            <input type="text" name="texto" class="form-control @error('texto') is-invalid @enderror" value="{{ old('texto', $pregunta?->texto) }}" id="texto" placeholder="Texto">
            {!! $errors->first('texto', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="tipo" class="form-label">{{ __('Tipo') }}</label>
            <input type="text" name="tipo" class="form-control @error('tipo') is-invalid @enderror" value="{{ old('tipo', $pregunta?->tipo) }}" id="tipo" placeholder="Tipo">
            {!! $errors->first('tipo', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-3">
            <label for="id_test">{{ __('Test Asociado') }}</label>
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
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>