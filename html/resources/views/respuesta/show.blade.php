@extends('adminlte::page')

@section('template_title')
    {{ $respuestum->name ?? __('Show') . " " . __('Respuestum') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Mostrar') }} Respuesta</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('respuesta.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Respuesta Texto:</strong>
                                    {{ $respuestum->respuesta_texto }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Texto Pregunta:</strong>
                                    {{ $respuestum->texto_pregunta }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Id Evaluacion:</strong>
                                    {{ $respuestum->id_evaluacion }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Id Pregunta:</strong>
                                    {{ $respuestum->id_pregunta }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
