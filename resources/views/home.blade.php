@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-12 text-center">
        <h1 class="m-0 text-primary font-weight-bold">Estadísticas</h1>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">

    {{-- TARJETAS RESUMEN --}}
    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="small-box bg-info shadow-sm">
          <div class="inner">
            <h3>{{ $voluntariosActivos }}</h3>
            <p>Activos</p>
          </div>
          <div class="icon"><i class="fas fa-users"></i></div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="small-box bg-secondary shadow-sm">
          <div class="inner">
            <h3>{{ $voluntariosInactivos }}</h3>
            <p>Inactivos</p>
          </div>
          <div class="icon"><i class="fas fa-user-slash"></i></div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="small-box bg-danger shadow-sm">
          <div class="inner">
            <h3>{{ $alertasRecientes }}</h3>
            <p>Alertas recientes</p>
          </div>
          <div class="icon"><i class="fas fa-heartbeat"></i></div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="small-box bg-success shadow-sm">
          <div class="inner">
            <h3>{{ $evaluacionesCompletadas }}</h3>
            <p>Evaluaciones completadas</p>
          </div>
          <div class="icon"><i class="fas fa-chart-bar"></i></div>
        </div>
      </div>
    </div>

    {{-- PANELES INFORMATIVOS --}}
    <div class="row">

      {{-- Voluntarios --}}
      <div class="col-lg-6">
        <div class="card card-outline card-primary shadow-sm">
          <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0">
              <i class="fas fa-users mr-2"></i>Últimos voluntarios registrados
            </h3>
          </div>
          <div class="card-body">
            <ul class="list-group list-group-flush">
              @forelse($ultimosVoluntarios as $vol)
                @php
                  $iniciales = $vol->iniciales ?? mb_substr($vol->nombres, 0, 1, 'UTF-8');
                  $estado = strtolower($vol->estado ?? '');
                @endphp
                <li class="list-group-item d-flex align-items-center">
                  <div class="rounded-circle bg-primary text-white text-center mr-3"
                       style="width:40px;height:40px;line-height:40px;font-weight:bold;">
                    {{ $iniciales }}
                  </div>
                  <div>
                    <strong>{{ $vol->nombres }} {{ $vol->apellidos }}</strong><br>
                    @if($estado === 'activo')
                      <small class="text-success font-weight-bold">Activo</small>
                    @elseif($estado === 'inactivo')
                      <small class="text-danger font-weight-bold">Inactivo</small>
                    @else
                      <small class="text-muted">Sin estado</small>
                    @endif
                  </div>
                </li>
              @empty
                <li class="list-group-item text-muted">
                  No hay voluntarios registrados todavía.
                </li>
              @endforelse
            </ul>
          </div>
        </div>
      </div>

      {{-- Reportes --}}
      {{-- Reportes --}}
      <div class="col-lg-6">
        <div class="card card-outline card-danger shadow-sm">
          <div class="card-header bg-danger text-white">
            <h3 class="card-title mb-0">
              <i class="fas fa-file-medical mr-2"></i>Últimos reportes generados
            </h3>
          </div>
          <div class="card-body">
            <ul class="list-group list-group-flush">

              @forelse($ultimosReportes as $rep)
                @php
                  // Inicial: “R” de Reporte o alguna letra fija
                  $inicial = 'R';

                  // Estado del reporte
                  $estado = $rep->estado_general ?? 'Sin estado';
                  $estadoLower = mb_strtolower($estado, 'UTF-8');

                  $estadoClass = $estadoLower === 'crítico' || $estadoLower === 'critico'
                      ? 'text-danger'
                      : ($estadoLower === 'pendiente' ? 'text-warning' : 'text-success');
                @endphp

                <li class="list-group-item d-flex align-items-center">
                  <div class="rounded-circle bg-danger text-white text-center mr-3"
                      style="width:40px;height:40px;line-height:40px;font-weight:bold;">
                    {{ $inicial }}
                  </div>
                  <div>
                    <strong>Reporte #{{ $rep->id }}</strong><br>
                    <small class="font-weight-bold {{ $estadoClass }}">
                      {{ $estado }}
                    </small><br>
                    @if($rep->fecha_generado)
                      <small class="text-muted">
                        {{ $rep->fecha_generado->format('d/m/Y H:i') }}
                      </small>
                    @endif
                  </div>
                </li>
              @empty
                <li class="list-group-item text-muted">
                  No hay reportes generados todavía.
                </li>
              @endforelse

            </ul>
          </div>
        </div>
      </div>


    </div>

    {{-- SECCION INFERIOR --}}
    <div class="row">

      {{-- Universidades --}}
      <div class="col-lg-4">
        <div class="card card-outline card-info shadow-sm">
          <div class="card-header bg-info text-white">
            <h4 class="card-title mb-0">
              <i class="fas fa-university mr-2"></i>Universidades
            </h4>
          </div>
          <div class="card-body">
            <ul class="list-unstyled mb-0">
              @forelse($universidadesData as $item)
                <li>
                  <i class="fas fa-circle text-info mr-2"></i>
                  {{ $item->label }}
                  <span class="badge badge-light ml-2">{{ $item->total }}</span>
                </li>
              @empty
                <li class="text-muted">Sin datos de universidades.</li>
              @endforelse
            </ul>
          </div>
        </div>
      </div>

      {{-- Necesidades --}}
      <div class="col-lg-4">
        <div class="card card-outline card-warning shadow-sm">
          <div class="card-header bg-warning">
            <h4 class="card-title mb-0 text-dark">
              <i class="fas fa-clipboard-list mr-2"></i>Necesidades
            </h4>
          </div>
          <div class="card-body">
            <ul class="list-unstyled mb-0">
              @forelse($necesidadesData as $item)
                <li>
                  <i class="fas fa-circle text-warning mr-2"></i>
                  {{ $item->label }}
                  <span class="badge badge-light ml-2">{{ $item->total }}</span>
                </li>
              @empty
                <li class="text-muted">Sin datos de necesidades.</li>
              @endforelse
            </ul>
          </div>
        </div>
      </div>

      {{-- Capacitaciones --}}
      <div class="col-lg-4">
        <div class="card card-outline card-success shadow-sm">
          <div class="card-header bg-success text-white">
            <h4 class="card-title mb-0">
              <i class="fas fa-chalkboard-teacher mr-2"></i>Capacitaciones
            </h4>
          </div>
          <div class="card-body">
            <ul class="list-unstyled mb-0">
              @forelse($capacitacionesData as $item)
                <li>
                  <i class="fas fa-circle text-success mr-2"></i>
                  {{ $item->label }}
                  <span class="badge badge-light ml-2">{{ $item->total }}</span>
                </li>
              @empty
                <li class="text-muted">Sin datos de capacitaciones.</li>
              @endforelse
            </ul>
          </div>
        </div>
      </div>

    </div>

  </div>
</section>

@endsection
