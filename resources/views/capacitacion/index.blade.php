@extends('adminlte::page')

@section('template_title')
    Capacitaciones
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span id="card_title">
                                {{ __('Capacitaciones') }}
                            </span>

                            <div class="float-right">
                                @can('gestionar_capacitaciones')
                                <a href="{{ route('capacitaciones.create') }}"
                                   class="btn btn-primary btn-sm float-right" data-placement="left">
                                    {{ __('Crear Nuevo') }}
                                </a>
                                @endcan
                            </div>
                        </div>
                    </div>

                    @if ($message = Session::get('success'))
                        <div class="alert alert-success m-4">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    <div class="card-body bg-white">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>
                                        <th>Nombre</th>
                                        <th>Descripción</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($capacitaciones as $capacitacion)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>
                                                <a href="#"
                                                   data-toggle="modal"
                                                   data-target="#modalDetalle{{ $capacitacion->id }}">
                                                    {{ $capacitacion->nombre }}
                                                </a>
                                            </td>

                                            <td>{{ $capacitacion->descripcion }}</td>
                                            

                                            <td class="text-center">
                                                @can('gestionar_capacitaciones')
                                                <a class="btn btn-sm btn-success"
                                                   href="{{ route('capacitaciones.edit', $capacitacion->id) }}">
                                                    <i class="fa fa-fw fa-edit"></i> Editar
                                                </a>
                                                @endcan

                                                @can('gestionar_capacitaciones')
                                                <button type="button"
                                                        class="btn btn-sm btn-info"
                                                        data-toggle="modal"
                                                        data-target="#modalCursos{{ $capacitacion->id }}">
                                                    <i class="fa fa-list"></i> Cursos
                                                </button>
                                                @endcan

                                                @can('gestionar_capacitaciones')
                                                <button type="button"
                                                        class="btn btn-danger btn-sm"
                                                        data-toggle="modal"
                                                        data-target="#modalEliminar{{ $capacitacion->id }}">
                                                    <i class="fa fa-fw fa-trash"></i> Eliminar
                                                </button>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

                {!! $capacitaciones->withQueryString()->links() !!}
            </div>
        </div>
    </div>

    {{-- ===================== MODALES POR CADA CAPACITACIÓN ===================== --}}
    @foreach($capacitaciones as $capacitacion)

        {{-- MODAL DETALLE GENERAL --}}
        <div class="modal fade" id="modalDetalle{{ $capacitacion->id }}" tabindex="-1" role="dialog"
             aria-labelledby="modalDetalleLabel{{ $capacitacion->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalDetalleLabel{{ $capacitacion->id }}">
                            Detalle de capacitación
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <h5>{{ $capacitacion->nombre }}</h5>
                        <p><strong>Descripción:</strong> {{ $capacitacion->descripcion }}</p>

                        @if($capacitacion->cursos->count())
                            <h6 class="mt-3">Cursos:</h6>
                            @foreach($capacitacion->cursos as $curso)
                                <div class="mb-3 border rounded p-2">
                                    <h6 class="mb-1">
                                        {{ $curso->nombre }}
                                        <small class="text-muted">
                                            ({{ $curso->etapas->count() }} etapas)
                                        </small>
                                    </h6>
                                    @if($curso->descripcion)
                                        <p class="mb-2">{{ $curso->descripcion }}</p>
                                    @endif
                                    @if($curso->etapas->count())
                                        <ol class="mb-0">
                                            @foreach($curso->etapas->sortBy('orden') as $etapa)
                                                <li>
                                                    {{ $etapa->nombre }}
                                                    @if(!is_null($etapa->orden))
                                                        <small class="text-muted">(#{{ $etapa->orden }})</small>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ol>
                                    @else
                                        <p class="text-muted mb-0">Sin etapas registradas.</p>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">No hay cursos registrados para esta capacitación.</p>
                        @endif
                    </div>

                    <div class="modal-footer">
                        <a href="{{ route('capacitaciones.edit', $capacitacion->id) }}"
                           class="btn btn-primary">
                            Gestionar cursos
                        </a>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL SOLO DE CURSOS --}}
        <div class="modal fade" id="modalCursos{{ $capacitacion->id }}" tabindex="-1" role="dialog"
             aria-labelledby="modalCursosLabel{{ $capacitacion->id }}" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="modalCursosLabel{{ $capacitacion->id }}">
                            Cursos de: {{ $capacitacion->nombre }}
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        @if($capacitacion->cursos->count())
                            @foreach($capacitacion->cursos as $curso)
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <strong>{{ $curso->nombre }}</strong>
                                        <span class="badge badge-primary ml-2">
                                            {{ $curso->etapas->count() }} etapas
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        @if($curso->descripcion)
                                            <p>{{ $curso->descripcion }}</p>
                                        @endif

                                        @if($curso->etapas->count())
                                            <ol class="mb-0">
                                                @foreach($curso->etapas->sortBy('orden') as $etapa)
                                                    <li>
                                                        {{ $etapa->nombre }}
                                                        @if(!is_null($etapa->orden))
                                                            <small class="text-muted">(#{{ $etapa->orden }})</small>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ol>
                                        @else
                                            <p class="text-muted mb-0">No hay etapas registradas.</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">No hay cursos registrados para esta capacitación.</p>
                        @endif
                    </div>

                    <div class="modal-footer">
                        <a href="{{ route('capacitaciones.edit', $capacitacion->id) }}"
                           class="btn btn-primary">
                            Gestionar cursos
                        </a>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL ELIMINAR --}}
        <div class="modal fade" id="modalEliminar{{ $capacitacion->id }}" tabindex="-1" role="dialog"
             aria-labelledby="modalEliminarLabel{{ $capacitacion->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="modalEliminarLabel{{ $capacitacion->id }}">
                            Eliminar capacitación
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        ¿Seguro que deseas eliminar la capacitación
                        <strong>{{ $capacitacion->nombre }}</strong>? Esta acción no se puede deshacer.
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>

                        <form action="{{ route('capacitaciones.destroy', $capacitacion->id) }}"
                              method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    @endforeach
@endsection
