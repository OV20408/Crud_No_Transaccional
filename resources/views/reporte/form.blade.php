<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="estado_general" class="form-label">{{ __('Estado General') }}</label>
            <input type="text" name="estado_general" class="form-control @error('estado_general') is-invalid @enderror" value="{{ old('estado_general', $reporte?->estado_general) }}" id="estado_general" placeholder="Estado General">
            {!! $errors->first('estado_general', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="fecha_generado" class="form-label">{{ __('Fecha Generado') }}</label>
            <input type="datetime-local" name="fecha_generado"
                class="form-control @error('fecha_generado') is-invalid @enderror"
                value="{{ old('fecha_generado', isset($reporte->fecha_generado) ? \Carbon\Carbon::parse($reporte->fecha_generado)->format('Y-m-d\TH:i') : '') }}"
                id="fecha_generado">
            {!! $errors->first('fecha_generado', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="observaciones" class="form-label">{{ __('Observaciones') }}</label>
            <input type="text" name="observaciones" class="form-control @error('observaciones') is-invalid @enderror" value="{{ old('observaciones', $reporte?->observaciones) }}" id="observaciones" placeholder="Observaciones">
            {!! $errors->first('observaciones', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="recomendaciones" class="form-label">{{ __('Recomendaciones') }}</label>
            <input type="text" name="recomendaciones" class="form-control @error('recomendaciones') is-invalid @enderror" value="{{ old('recomendaciones', $reporte?->recomendaciones) }}" id="recomendaciones" placeholder="Recomendaciones">
            {!! $errors->first('recomendaciones', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="resumen_emocional" class="form-label">{{ __('Resumen Emocional') }}</label>
            <input type="text" name="resumen_emocional" class="form-control @error('resumen_emocional') is-invalid @enderror" value="{{ old('resumen_emocional', $reporte?->resumen_emocional) }}" id="resumen_emocional" placeholder="Resumen Emocional">
            {!! $errors->first('resumen_emocional', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="resumen_fisico" class="form-label">{{ __('Resumen Fisico') }}</label>
            <input type="text" name="resumen_fisico" class="form-control @error('resumen_fisico') is-invalid @enderror" value="{{ old('resumen_fisico', $reporte?->resumen_fisico) }}" id="resumen_fisico" placeholder="Resumen Fisico">
            {!! $errors->first('resumen_fisico', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-3">
            <label for="id_historial">{{ __('Historial Clínico') }}</label>
            <select name="id_historial" id="id_historial"
                class="form-control @error('id_historial') is-invalid @enderror" required>
                <option value="">Seleccione un historial</option>
                @foreach ($historiales as $historial)
                    <option value="{{ $historial->id }}"
                        {{ old('id_historial', $reporte->id_historial ?? '') == $historial->id ? 'selected' : '' }}>
                        {{ $historial->id }} - {{ $historial->email ?? 'Sin descripción' }}
                    </option>
                @endforeach
            </select>
            {!! $errors->first('id_historial', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>