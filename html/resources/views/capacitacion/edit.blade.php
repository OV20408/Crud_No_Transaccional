@extends('adminlte::page')

@section('template_title')
    Actualizar Capacitación
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">Actualizar Capacitación</span>
                    </div>

                    <div class="card-body bg-white">
                        <form method="POST"
                              action="{{ route('capacitaciones.update', $capacitacion->id) }}"
                              role="form">
                            @csrf
                            @method('PATCH')

                            {{-- Campos básicos (nombre, descripción, etc.) --}}
                            @include('capacitacion.form')

                            <hr>

                            {{-- Botón para abrir el modal de cursos --}}
                            <div class="form-group">
                                <label>Cursos y etapas</label><br>
                                <button type="button"
                                        class="btn btn-primary"
                                        data-toggle="modal"
                                        data-target="#modalCursos">
                                    <i class="fas fa-layer-group"></i> Gestionar cursos
                                </button>
                                <small class="form-text text-muted">
                                    Al guardar la capacitación se guardarán también los cursos y sus etapas.
                                </small>
                            </div>

                            {{-- Modal de gestión de cursos (mismo que usarás en create) --}}
                            @include('capacitacion._modal_cursos', ['capacitacion' => $capacitacion])

                            <div class="box-footer mt-3">
                                <button type="submit" class="btn btn-primary">
                                    Guardar cambios
                                </button>
                                <a href="{{ route('capacitaciones.index') }}"
                                   class="btn btn-secondary">
                                    Cancelar
                                </a>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
