<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="fecha" class="form-label">{{ __('Fecha de Evaluación') }}</label>
            <input type="datetime-local" name="fecha"
                class="form-control @error('fecha') is-invalid @enderror"
                value="{{ old('fecha', isset($evaluacion->fecha) ? \Carbon\Carbon::parse($evaluacion->fecha)->format('Y-m-d\TH:i') : '') }}"
                id="fecha" required>
            {!! $errors->first('fecha', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="id_reporte" class="form-label">{{ __('Reporte Asociado') }}</label>
            <select name="id_reporte" id="id_reporte"
                class="form-control @error('id_reporte') is-invalid @enderror" required>
                <option value="">Seleccione un reporte</option>
                @foreach ($reportes as $reporte)
                    <option value="{{ $reporte->id }}"
                        {{ old('id_reporte', $evaluacion->id_reporte ?? '') == $reporte->id ? 'selected' : '' }}>
                        {{ $reporte->id }} - {{ $reporte->estado_general ?? 'Sin estado' }}
                    </option>
                @endforeach
            </select>
            {!! $errors->first('id_reporte', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="id_test" class="form-label">{{ __('Test Aplicado') }}</label>
            <select name="id_test" id="id_test"
                class="form-control @error('id_test') is-invalid @enderror" required>
                <option value="">Seleccione un test</option>
                @foreach ($tests as $test)
                    <option value="{{ $test->id }}"
                        {{ old('id_test', $evaluacion->id_test ?? '') == $test->id ? 'selected' : '' }}>
                        {{ $test->id }} - {{ $test->nombre ?? 'Sin nombre' }}
                    </option>
                @endforeach
            </select>
            {!! $errors->first('id_test', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="id_universidad" class="form-label">{{ __('Universidad') }}</label>
            <select name="id_universidad" id="id_universidad"
                class="form-control @error('id_universidad') is-invalid @enderror" required>
                <option value="">Seleccione una universidad</option>
                @foreach ($universidades as $uni)
                    <option value="{{ $uni->id }}"
                        {{ old('id_universidad', $evaluacion->id_universidad ?? '') == $uni->id ? 'selected' : '' }}>
                        {{ $uni->id }} - {{ $uni->nombre ?? 'Sin nombre' }}
                    </option>
                @endforeach
            </select>
            {!! $errors->first('id_universidad', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Guardar') }}</button>
    </div>
</div>