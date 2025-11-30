@extends('adminlte::page')

@section('template_title')
    {{ __('Create') }} Historial Clinico
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Crear') }} Historial Clinico</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('historial_clinico.store') }}"  role="form" enctype="multipart/form-data">
                            @csrf

                            @include('historial_clinico.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
