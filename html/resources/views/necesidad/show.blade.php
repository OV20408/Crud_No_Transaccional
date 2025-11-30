@extends('adminlte::page')

@section('template_title')
    {{ $necesidad->name ?? __('Show') . " " . __('Necesidad') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Mostrar') }} Necesidad</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('necesidades.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Descripcion:</strong>
                                    {{ $necesidad->descripcion }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Tipo:</strong>
                                    {{ $necesidad->tipo }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
