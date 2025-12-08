@extends('adminlte::page')

@section('template_title')
    Cursos
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Cursos') }}
                            </span>

                             <div class="float-right">
                                @can('crear_cursos')
                                <a href="{{ route('curso.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        
									<th >Descripcion</th>
									<th >Nombre</th>
									<th >ID Capacitacion</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cursos as $curso)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
										<td >{{ $curso->descripcion }}</td>
										<td >{{ $curso->nombre }}</td>
                                        <td>{{ $curso->capacitacion->nombre ?? '—' }}</td>

                                            <td>
                                                <form action="{{ route('curso.destroy', $curso->id) }}" method="POST">
                                                    @can('gestionar_capacitaciones')
                                                    <a class="btn btn-sm btn-primary " href="{{ route('curso.show', $curso->id) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Mostrar') }}</a>
                                                    @endcan
                                                    @can('editar_cursos')
                                                    <a class="btn btn-sm btn-success" href="{{ route('curso.edit', $curso->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Editar') }}</a>
                                                    @endcan
                                                    @can('editar_cursos')
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="event.preventDefault(); confirm('Are you sure to delete?') ? this.closest('form').submit() : false;"><i class="fa fa-fw fa-trash"></i> {{ __('Eliminar') }}</button>
                                                    @endcan
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {!! $cursos->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@endsection
