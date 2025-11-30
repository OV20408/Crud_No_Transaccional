@extends('adminlte::page')

@section('template_title')
    {{ __('Create') }} Universidad
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Crear') }} Universidad</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('universidades.store') }}"  role="form" enctype="multipart/form-data">
                            @csrf

                            @include('universidad.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
