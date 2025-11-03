<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $historialClinico?->email) }}" id="email" placeholder="Email">
            {!! $errors->first('email', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="fecha_actualizacion" class="form-label">{{ __('Fecha Actualizacion') }}</label>
            <input type="datetime-local" name="fecha_actualizacion" class="form-control @error('fecha_actualizacion') is-invalid @enderror" value="{{ old('fecha_actualizacion', $historialClinico?->fecha_actualizacion?->format('Y-m-d\TH:i')) }}" id="fecha_actualizacion">
            {!! $errors->first('fecha_actualizacion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

        <div class="form-group mb-2 mb20">
            <label for="fecha_inicio" class="form-label">{{ __('Fecha Inicio') }}</label>
            <input type="datetime-local" name="fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @enderror" value="{{ old('fecha_inicio', $historialClinico?->fecha_inicio?->format('Y-m-d\TH:i')) }}" id="fecha_inicio">
            {!! $errors->first('fecha_inicio', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>