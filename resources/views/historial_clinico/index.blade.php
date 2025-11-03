@extends('adminlte::page')

@section('template_title')
    Historial Clinicos
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Historial Clinicos') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('historial_clinico.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
                                  {{ __('Create New') }}
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
                                        
									<th >Email</th>
									<th >Fecha Actualizacion</th>
									<th >Fecha Inicio</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($historial_clinico as $historialClinico)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
										<td >{{ $historialClinico->email }}</td>
										<td >{{ $historialClinico->fecha_actualizacion }}</td>
										<td >{{ $historialClinico->fecha_inicio }}</td>

                                            <td>
                                                <form action="{{ route('historial_clinico.destroy', $historialClinico->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('historial_clinico.show', $historialClinico->id) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Show') }}</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('historial_clinico.edit', $historialClinico->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}</a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="event.preventDefault(); confirm('Are you sure to delete?') ? this.closest('form').submit() : false;"><i class="fa fa-fw fa-trash"></i> {{ __('Delete') }}</button>
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
