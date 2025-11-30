@extends('adminlte::page')

@section('template_title')
    Progreso Voluntarios
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Progreso Voluntarios') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('progreso-voluntario.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        
									<th >Estado</th>
									<th >Fecha Finalizacion</th>
									<th >Fecha Inicio</th>
									<th >Id Etapa</th>
									<th >Id Usuario</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($progresoVoluntarios as $progresoVoluntario)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
										<td >{{ $progresoVoluntario->estado }}</td>
										<td >{{ $progresoVoluntario->fecha_finalizacion ? \Carbon\Carbon::parse($progresoVoluntario->fecha_finalizacion)->format('d/m/Y H:i') : '—' }}</td>
										<td >{{ $progresoVoluntario->fecha_inicio ? \Carbon\Carbon::parse($progresoVoluntario->fecha_inicio)->format('d/m/Y H:i') : '—' }}</td>
										<td >{{ $progresoVoluntario->id_etapa }}</td>
										<td >{{ $progresoVoluntario->id_usuario }}</td>

                                            <td>
                                                <form action="{{ route('progreso-voluntario.destroy', $progresoVoluntario->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('progreso-voluntario.show', $progresoVoluntario->id) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Mostrar') }}</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('progreso-voluntario.edit', $progresoVoluntario->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Editar') }}</a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="event.preventDefault(); confirm('Are you sure to delete?') ? this.closest('form').submit() : false;"><i class="fa fa-fw fa-trash"></i> {{ __('Eliminar') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $progresoVoluntarios->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@endsection
