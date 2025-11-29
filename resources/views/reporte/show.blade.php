@extends('adminlte::page')

@section('template_title')
    {{ $reporte->name ?? __('Show') . " " . __('Reporte') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Mostrar') }} Reporte</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('reportes.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Estado General:</strong>
                                    {{ $reporte->estado_general }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Fecha Generado:</strong>
                                    {{ $reporte->fecha_generado ? \Carbon\Carbon::parse($reporte->fecha_generado)->format('d/m/Y H:i') : '—' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Observaciones:</strong>
                                    {{ $reporte->observaciones }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Recomendaciones:</strong>
                                    {{ $reporte->recomendaciones }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Resumen Emocional:</strong>
                                    {{ $reporte->resumen_emocional }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Resumen Fisico:</strong>
                                    {{ $reporte->resumen_fisico }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Id Historial:</strong>
                                    {{ $reporte->id_historial }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
