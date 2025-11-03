<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="respuesta_texto" class="form-label">{{ __('Texto de la Respuesta') }}</label>
            <input type="text" name="respuesta_texto"
                class="form-control @error('respuesta_texto') is-invalid @enderror"
                value="{{ old('respuesta_texto', $respuesta?->respuesta_texto) }}" id="respuesta_texto"
                placeholder="Ingrese la respuesta">
            {!! $errors->first('respuesta_texto', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="texto_pregunta" class="form-label">{{ __('Texto de la Pregunta') }}</label>
            <input type="text" name="texto_pregunta"
                class="form-control @error('texto_pregunta') is-invalid @enderror"
                value="{{ old('texto_pregunta', $respuesta?->texto_pregunta) }}" id="texto_pregunta"
                placeholder="Ingrese el texto de la pregunta">
            {!! $errors->first('texto_pregunta', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="id_evaluacion" class="form-label">{{ __('Evaluación Asociada') }}</label>
            <select name="id_evaluacion" id="id_evaluacion"
                class="form-control @error('id_evaluacion') is-invalid @enderror" required>
                <option value="">Seleccione una evaluación</option>
                @foreach ($evaluaciones as $evaluacion)
                    <option value="{{ $evaluacion->id }}"
                        {{ old('id_evaluacion', $respuesta->id_evaluacion ?? '') == $evaluacion->id ? 'selected' : '' }}>
                        {{ $evaluacion->id }} - {{ \Carbon\Carbon::parse($evaluacion->fecha)->format('d/m/Y H:i') }}
                    </option>
                @endforeach
            </select>
            {!! $errors->first('id_evaluacion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="id_pregunta" class="form-label">{{ __('Pregunta Asociada') }}</label>
            <select name="id_pregunta" id="id_pregunta"
                class="form-control @error('id_pregunta') is-invalid @enderror" required>
                <option value="">Seleccione una pregunta</option>
                @foreach ($preguntas as $pregunta)
                    <option value="{{ $pregunta->id }}"
                        {{ old('id_pregunta', $respuesta->id_pregunta ?? '') == $pregunta->id ? 'selected' : '' }}>
                        {{ $pregunta->id }} - {{ $pregunta->texto ?? 'Sin texto' }}
                    </option>
                @endforeach
            </select>
            {!! $errors->first('id_pregunta', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>