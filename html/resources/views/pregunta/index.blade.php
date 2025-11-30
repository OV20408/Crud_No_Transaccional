@extends('adminlte::page')

@section('template_title')
    Pregunta
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Pregunta') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('pregunta.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        
									<th >Texto</th>
									<th >Tipo</th>
									<th >Id Test</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pregunta as $preguntum)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
										<td >{{ $preguntum->texto }}</td>
										<td >{{ $preguntum->tipo }}</td>
										<td >{{ $preguntum->id_test }}</td>

                                            <td>
                                                <form action="{{ route('pregunta.destroy', $preguntum->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('pregunta.show', $preguntum->id) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Mostrar') }}</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('pregunta.edit', $preguntum->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Editar') }}</a>
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
                {!! $pregunta->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@endsection
