@extends('adminlte::page')

@section('template_title')
    {{ $preguntum->name ?? __('Show') . " " . __('Preguntum') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Mostrar') }} Pregunta</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('pregunta.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Texto:</strong>
                                    {{ $preguntum->texto }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Tipo:</strong>
                                    {{ $preguntum->tipo }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Id Test:</strong>
                                    {{ $preguntum->id_test }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
