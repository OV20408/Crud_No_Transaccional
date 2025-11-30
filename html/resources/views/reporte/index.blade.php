@extends('adminlte::page')

@section('template_title')
    Reportes
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Reportes') }}
                            </span>

                             <div class="float-right">
                                <a href="{{ route('reportes.create') }}" class="btn btn-primary btn-sm float-right"  data-placement="left">
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
                                        <th>Id</th>
                                        
									<th >Estado General</th>
									<th >Fecha Generado</th>
									<th >Observaciones</th>
									<th >Recomendaciones</th>
									<th >Resumen Emocional</th>
									<th >Resumen Fisico</th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reportes as $reporte)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            
										<td >{{ $reporte->estado_general }}</td>
										<td >{{ $reporte->fecha_generado ? \Carbon\Carbon::parse($reporte->fecha_generado)->format('d/m/Y H:i') : '—' }}</td>
										<td >{{ $reporte->observaciones }}</td>
										<td >{{ $reporte->recomendaciones }}</td>
										<td >{{ $reporte->resumen_emocional }}</td>
										<td >{{ $reporte->resumen_fisico }}</td>

                                            <td>
                                                <form action="{{ route('reportes.destroy', $reporte->id) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary " href="{{ route('reportes.show', $reporte->id) }}"><i class="fa fa-fw fa-eye"></i> {{ __('Mostrar') }}</a>
                                                    <a class="btn btn-sm btn-success" href="{{ route('reportes.edit', $reporte->id) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Editar') }}</a>
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
                {!! $reportes->withQueryString()->links() !!}
            </div>
        </div>
    </div>
@endsection
