@extends('adminlte::page')

@section('template_title')
    Respuesta
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Respuesta') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('respuesta.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        
									<th >Respuesta Texto</th>
									<th >Texto Pregunta</th>
									<th >Id Evaluacion</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($respuesta as $respuestum)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
										<td >{{ $respuestum->respuesta_texto }}</td>
										<td >{{ $respuestum->texto_pregunta }}</td>
										<td >{{ $respuestum->id_evaluacion }}</td>

                                            <td>
                                                <form action="{{ route('respuesta.destroy', $respuestum->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('respuesta.show', $respuestum->id) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Mostrar') }}</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('respuesta.edit', $respuestum->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Editar') }}</a>
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
                {!! $respuesta->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@endsection
