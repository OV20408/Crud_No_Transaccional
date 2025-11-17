<div class="row padding-1 p-1">
    <div class="col-md-12">

        {{-- Texto de la Respuesta --}}
        <div class="form-group mb-2">
            <label for="respuesta_texto">Texto de la Respuesta</label>
            <input type="text" name="respuesta_texto"
                class="form-control @error('respuesta_texto') is-invalid @enderror"
                value="{{ old('respuesta_texto', $respuesta->respuesta_texto ?? '') }}"
                placeholder="Ingrese la respuesta">
            {!! $errors->first('respuesta_texto', '<div class="invalid-feedback">:message</div>') !!}
        </div>

        {{-- Pregunta Asociada (solo selección) --}}
        <div class="form-group mb-2">
            <label for="pregunta_id">Pregunta Asociada</label>
            <select name="pregunta_id"
                    class="form-control @error('pregunta_id') is-invalid @enderror" required>
                <option value="">Seleccione una pregunta</option>
                @foreach ($preguntas as $pregunta)
                    <option value="{{ $pregunta->id }}"
                        {{ old('pregunta_id', $respuesta->id_pregunta ?? '') == $pregunta->id ? 'selected' : '' }}>
                        {{ $pregunta->id }} - {{ $pregunta->texto }}
                    </option>
                @endforeach
            </select>
            {!! $errors->first('pregunta_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>

        {{-- Evaluación Asociada --}}
        <div class="form-group mb-2">
            <label for="id_evaluacion">Evaluación Asociada</label>
            <select name="id_evaluacion"
                    class="form-control @error('id_evaluacion') is-invalid @enderror" required>
                <option value="">Seleccione una evaluación</option>
                @foreach ($evaluaciones as $evaluacion)
                    <option value="{{ $evaluacion->id }}"
                        {{ old('id_evaluacion', $respuesta->id_evaluacion ?? '') == $evaluacion->id ? 'selected' : '' }}>
                        {{ $evaluacion->id }} - {{ \Carbon\Carbon::parse($evaluacion->fecha)->format('d/m/Y H:i') }}
                    </option>
                @endforeach
            </select>
            {!! $errors->first('id_evaluacion', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>

    <div class="col-md-12 mt-2">
        <button type="submit" class="btn btn-primary">Guardar</button>
    </div>
</div>
