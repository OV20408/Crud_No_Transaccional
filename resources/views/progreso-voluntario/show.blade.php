@extends('adminlte::page')

@section('template_title')
    {{ $progresoVoluntario->name ?? __('Show') . " " . __('Progreso Voluntario') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Mostrar') }} Progreso Voluntario</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('progreso-voluntario.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Estado:</strong>
                                    {{ $progresoVoluntario->estado }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Fecha Finalizacion:</strong>
                                    {{ $progresoVoluntario->fecha_finalizacion ? \Carbon\Carbon::parse($progresoVoluntario->fecha_finalizacion)->format('d/m/Y H:i') : '—' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Fecha Inicio:</strong>
                                    {{ $progresoVoluntario->fecha_inicio ? \Carbon\Carbon::parse($progresoVoluntario->fecha_inicio)->format('d/m/Y H:i') : '—' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Id Etapa:</strong>
                                    {{ $progresoVoluntario->id_etapa }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Id Usuario:</strong>
                                    {{ $progresoVoluntario->id_usuario }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
