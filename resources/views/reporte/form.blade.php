<div class="row padding-1 p-1">
    <div class="col-md-12">

        <div class="form-group mb-2 mb20">
            <label for="estado_general" class="form-label">Estado General</label>
            <input type="text" name="estado_general" class="form-control @error('estado_general') is-invalid @enderror"
                value="{{ old('estado_general', $reporte?->estado_general) }}" placeholder="Estado General">
            {!! $errors->first('estado_general', '<div class="invalid-feedback">:message</div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="fecha_generado" class="form-label">Fecha Generado</label>
            <input type="datetime-local" name="fecha_generado" class="form-control @error('fecha_generado') is-invalid @enderror"
                value="{{ old('fecha_generado', isset($reporte->fecha_generado) ? \Carbon\Carbon::parse($reporte->fecha_generado)->format('Y-m-d\TH:i') : '') }}">
            {!! $errors->first('fecha_generado', '<div class="invalid-feedback">:message</div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="observaciones" class="form-label">Observaciones</label>
            <textarea name="observaciones" class="form-control @error('observaciones') is-invalid @enderror"
                rows="3">{{ old('observaciones', $reporte?->observaciones) }}</textarea>
            {!! $errors->first('observaciones', '<div class="invalid-feedback">:message</div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="recomendaciones" class="form-label">Recomendaciones</label>
            <input type="text" name="recomendaciones" class="form-control @error('recomendaciones') is-invalid @enderror"
                value="{{ old('recomendaciones', $reporte?->recomendaciones) }}" placeholder="Recomendaciones">
            {!! $errors->first('recomendaciones', '<div class="invalid-feedback">:message</div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="resumen_emocional" class="form-label">Resumen Emocional</label>
            <textarea name="resumen_emocional" class="form-control @error('resumen_emocional') is-invalid @enderror"
                rows="2">{{ old('resumen_emocional', $reporte?->resumen_emocional) }}</textarea>
            {!! $errors->first('resumen_emocional', '<div class="invalid-feedback">:message</div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="resumen_fisico" class="form-label">Resumen Físico</label>
            <textarea name="resumen_fisico" class="form-control @error('resumen_fisico') is-invalid @enderror"
                rows="2">{{ old('resumen_fisico', $reporte?->resumen_fisico) }}</textarea>
            {!! $errors->first('resumen_fisico', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">Guardar</button>
    </div>
</div>
