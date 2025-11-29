@extends('adminlte::page')

@section('template_title')
    {{ $evaluacion->name ?? __('Show') . " " . __('Evaluacion') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Mostrar') }} Evaluacion</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('evaluacion.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Fecha:</strong>
                                    {{ $evaluacion->fecha ? \Carbon\Carbon::parse($evaluacion->fecha)->format('d/m/Y H:i') : '—' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Id Reporte:</strong>
                                    {{ $evaluacion->id_reporte }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Id Test:</strong>
                                    {{ $evaluacion->id_test }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Id Universidad:</strong>
                                    {{ $evaluacion->id_universidad }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
