@extends('adminlte::page')

@section('template_title')
    {{ $curso->name ?? __('Show') . " " . __('Curso') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Mostrar') }} Curso</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('curso.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        <div class="form-group mb-2 mb20">
                            <strong>Descripcion:</strong>
                            {{ $curso->descripcion }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Nombre:</strong>
                            {{ $curso->nombre }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Capacitacion:</strong>
                            {{ $curso->capacitacion->nombre ?? '—' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
