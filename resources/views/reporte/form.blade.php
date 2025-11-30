<div class="row padding-1 p-1">
    <div class="col-md-12">

        <div class="form-group mb-2 mb20">
            <label for="estado_general" class="form-label">Estado General <span class="text-danger">*</span></label>
            <select name="estado_general" class="form-control @error('estado_general') is-invalid @enderror" required>
                <option value="">Seleccione el estado</option>
                <option value="Excelente" {{ old('estado_general', $reporte?->estado_general) == 'Excelente' ? 'selected' : '' }}>Excelente</option>
                <option value="Bueno" {{ old('estado_general', $reporte?->estado_general) == 'Bueno' ? 'selected' : '' }}>Bueno</option>
                <option value="Regular" {{ old('estado_general', $reporte?->estado_general) == 'Regular' ? 'selected' : '' }}>Regular</option>
                <option value="Requiere atencion" {{ old('estado_general', $reporte?->estado_general) == 'Requiere atencion' ? 'selected' : '' }}>Requiere atención</option>
            </select>
            {!! $errors->first('estado_general', '<div class="invalid-feedback">:message</div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="fecha_generado" class="form-label">Fecha Generado <span class="text-danger">*</span></label>
            <input type="datetime-local" name="fecha_generado" class="form-control @error('fecha_generado') is-invalid @enderror"
                value="{{ old('fecha_generado', isset($reporte->fecha_generado) ? \Carbon\Carbon::parse($reporte->fecha_generado)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" required>
            {!! $errors->first('fecha_generado', '<div class="invalid-feedback">:message</div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="resumen_fisico" class="form-label">Resumen Físico</label>
            <textarea name="resumen_fisico" class="form-control @error('resumen_fisico') is-invalid @enderror"
                rows="3" placeholder="Descripción del estado físico del voluntario" maxlength="1000">{{ old('resumen_fisico', $reporte?->resumen_fisico) }}</textarea>
            {!! $errors->first('resumen_fisico', '<div class="invalid-feedback">:message</div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="resumen_emocional" class="form-label">Resumen Emocional</label>
            <textarea name="resumen_emocional" class="form-control @error('resumen_emocional') is-invalid @enderror"
                rows="3" placeholder="Descripción del estado emocional del voluntario" maxlength="1000">{{ old('resumen_emocional', $reporte?->resumen_emocional) }}</textarea>
            {!! $errors->first('resumen_emocional', '<div class="invalid-feedback">:message</div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="observaciones" class="form-label">Observaciones</label>
            <textarea name="observaciones" class="form-control @error('observaciones') is-invalid @enderror"
                rows="3" placeholder="Observaciones adicionales" maxlength="1000">{{ old('observaciones', $reporte?->observaciones) }}</textarea>
            {!! $errors->first('observaciones', '<div class="invalid-feedback">:message</div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="recomendaciones" class="form-label">Recomendaciones</label>
            <textarea name="recomendaciones" class="form-control @error('recomendaciones') is-invalid @enderror"
                rows="3" placeholder="Recomendaciones para el voluntario" maxlength="1000">{{ old('recomendaciones', $reporte?->recomendaciones) }}</textarea>
            {!! $errors->first('recomendaciones', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">Guardar</button>
    </div>
</div>
