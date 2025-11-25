@extends('adminlte::page')

@section('template_title')
    {{ __('Update') }} Historial Clinico
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Actualizar') }} Historial Clinico</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('historial_clinico.update', $historialClinico->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('historial_clinico.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
