@extends('adminlte::page')

@section('content')
<div class="container">

    <h2 class="mb-4">📨 Mensajes de Voluntarios</h2>

    <div class="card shadow-sm">
        <div class="card-body p-0">

            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Voluntario</th>
                        <th>Mensaje</th>
                        <th>Respuesta</th>
                        <th>Estado</th>
                        <th width="240px">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($consultas as $c)
                    <tr>
                        <td>
                            <strong>{{ $c->nombres }} {{ $c->apellidos }}</strong><br>
                            <small class="text-muted">CI: {{ $c->ci }}</small>
                        </td>

                        <td>{{ $c->mensaje }}</td>

                        <td>
                            @if($c->respuesta_admin)
                                <span class="text-success">{{ $c->respuesta_admin }}</span>
                            @else
                                <span class="text-muted">Sin respuesta</span>
                            @endif
                        </td>

                        <td>
                            @if($c->estado == 'pendiente')
                                <span class="badge bg-warning text-dark">Pendiente</span>
                            @else
                                <span class="badge bg-success">Respondido</span>
                            @endif
                        </td>

                        <td>
                            @if($c->estado == 'pendiente')
                            <form action="{{ url('consultas/'.$c->id.'/responder') }}" method="POST" class="d-flex">
                                @csrf
                                <input type="text"
                                    name="respuesta_admin"
                                    class="form-control me-2"
                                    placeholder="Escribir respuesta..."
                                >
                                <button class="btn btn-primary btn-sm">Enviar</button>
                            </form>
                            @else
                                <button class="btn btn-outline-secondary btn-sm" disabled>
                                    Ya respondido
                                </button>
                            @endif
                        </td>

                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>

</div>
@endsection
