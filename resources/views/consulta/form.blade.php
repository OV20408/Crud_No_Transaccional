<div class="row p-1">
    <div class="col-md-12">

        {{-- VOLUNTARIO --}}
        <div class="form-group mb-3">
            <label for="voluntario_id" class="form-label">Voluntario</label>
            <select name="voluntario_id" id="voluntario_id"
                    class="form-control @error('voluntario_id') is-invalid @enderror" required>
                <option value="">Seleccione un voluntario</option>
                @foreach($voluntarios as $v)
                    <option value="{{ $v->id_usuario }}"
                        {{ old('voluntario_id', $consulta->voluntario_id) == $v->id_usuario ? 'selected' : '' }}>
                        {{ $v->nombres }} {{ $v->apellidos }} — CI: {{ $v->ci }}
                    </option>
                @endforeach
            </select>
            {!! $errors->first('voluntario_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>

        {{-- NECESIDAD --}}
        <div class="form-group mb-3">
            <label for="necesidad_id" class="form-label">Necesidad</label>
            <select name="necesidad_id" id="necesidad_id"
                    class="form-control @error('necesidad_id') is-invalid @enderror" required>
                <option value="">Seleccione una necesidad</option>
                @foreach($necesidades as $n)
                    <option value="{{ $n->id }}"
                        {{ old('necesidad_id', $consulta->necesidad_id) == $n->id ? 'selected' : '' }}>
                        {{ $n->tipo }} — {{ $n->descripcion }}
                    </option>
                @endforeach
            </select>
            {!! $errors->first('necesidad_id', '<div class="invalid-feedback">:message</div>') !!}
        </div>

        {{-- MENSAJE --}}
        <div class="form-group mb-3">
            <label for="mensaje" class="form-label">Mensaje</label>
            <textarea name="mensaje" id="mensaje"
                      class="form-control @error('mensaje') is-invalid @enderror"
                      placeholder="Escriba el mensaje..."
                      rows="3">{{ old('mensaje', $consulta->mensaje) }}</textarea>
            {!! $errors->first('mensaje', '<div class="invalid-feedback">:message</div>') !!}
        </div>

        {{-- ESTADO --}}
        <div class="form-group mb-3">
            <label for="estado" class="form-label">Estado</label>
            <select name="estado" id="estado"
                    class="form-control @error('estado') is-invalid @enderror" required>
                <option value="Pendiente" {{ old('estado', $consulta->estado) == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="Revisando" {{ old('estado', $consulta->estado) == 'Revisando' ? 'selected' : '' }}>Revisando</option>
                <option value="Resuelto" {{ old('estado', $consulta->estado) == 'Resuelto' ? 'selected' : '' }}>Resuelto</option>
            </select>
            {!! $errors->first('estado', '<div class="invalid-feedback">:message</div>') !!}
        </div>

    </div>

    <div class="col-md-12 mt-2">
        <button type="submit" class="btn btn-primary w-100">Guardar</button>
    </div>
</div>
