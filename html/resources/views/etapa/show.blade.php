@extends('adminlte::page')

@section('template_title')
    {{ $etapa->name ?? __('Show') . " " . __('Etapa') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Mostrar') }} Etapa</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('etapas.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Nombre:</strong>
                                    {{ $etapa->nombre }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Orden:</strong>
                                    {{ $etapa->orden }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>ID Curso:</strong>
                                    {{ $etapa->id_curso }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
