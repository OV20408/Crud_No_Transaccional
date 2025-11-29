@extends('adminlte::page')

@section('template_title')
    {{ $historialClinico->name ?? __('Show') . " " . __('Historial Clinico') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Mostrar') }} Historial Clinico</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('historial_clinico.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Email:</strong>
                                    {{ $historialClinico->email }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Fecha Actualizacion:</strong>
                                    {{ $historialClinico->fecha_actualizacion ? \Carbon\Carbon::parse($historialClinico->fecha_actualizacion)->format('d/m/Y H:i') : '—' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Fecha Inicio:</strong>
                                    {{ $historialClinico->fecha_inicio ? \Carbon\Carbon::parse($historialClinico->fecha_inicio)->format('d/m/Y H:i') : '—' }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
