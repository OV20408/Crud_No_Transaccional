@extends('adminlte::page')

@section('template_title')
    {{ $consulta->name ?? __('Show') . " " . __('Consulta') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Consulta</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('consultas-web.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Voluntario Id:</strong>
                                    {{ $consulta->voluntario_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Necesidad Id:</strong>
                                    {{ $consulta->necesidad_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Mensaje:</strong>
                                    {{ $consulta->mensaje }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Estado:</strong>
                                    {{ $consulta->estado }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
