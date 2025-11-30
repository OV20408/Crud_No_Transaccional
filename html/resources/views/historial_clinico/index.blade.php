@extends('adminlte::page')

@section('template_title')
    Historial Clínico
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span id="card_title">
                            {{ __('Historial Clínico') }}
                        </span>

                        <div class="float-right">
                            <a href="{{ route('historial_clinico.create') }}" class="btn btn-primary btn-sm float-right" data-placement="left">
                                {{ __('Crear Nuevo') }}
                            </a>
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
                                    <th>Usuario</th>
                                    <th>CI</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Actualización</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($historial_clinico as $historialClinico)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        
                                        {{-- Usuario asociado --}}
                                        <td>
                                            {{ $historialClinico->usuario?->nombres ?? '—' }}
                                            {{ $historialClinico->usuario?->apellidos ?? '' }}
                                        </td>

                                        {{-- Carnet de identidad --}}
                                        <td>{{ $historialClinico->usuario?->ci ?? '—' }}</td>

                                        {{-- Fechas --}}
                                        <td>{{ $historialClinico->fecha_inicio ? \Carbon\Carbon::parse($historialClinico->fecha_inicio)->format('d/m/Y H:i') : '—' }}</td>
                                        <td>{{ $historialClinico->fecha_actualizacion ? \Carbon\Carbon::parse($historialClinico->fecha_actualizacion)->format('d/m/Y H:i') : '—' }}</td>

                                        <td>
                                            <form action="{{ route('historial_clinico.destroy', $historialClinico->id) }}" method="POST">
                                                <a class="btn btn-sm btn-primary" href="{{ route('historial_clinico.show', $historialClinico->id) }}">
                                                    <i class="fa fa-fw fa-eye"></i> {{ __('Mostrar') }}
                                                </a>
                                                <a class="btn btn-sm btn-success" href="{{ route('historial_clinico.edit', $historialClinico->id) }}">
                                                    <i class="fa fa-fw fa-edit"></i> {{ __('Editar') }}
                                                </a>
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="event.preventDefault(); confirm('¿Seguro que deseas eliminar este registro?') ? this.closest('form').submit() : false;">
                                                    <i class="fa fa-fw fa-trash"></i> {{ __('Eliminar') }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {!! $historial_clinico->withQueryString()->links() !!}
        </div>
    </div>
</div>
@endsection
