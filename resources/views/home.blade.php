@extends('adminlte::page')

@section('title', 'Dashboard')

@section('css')
<style>
  /* Altura mínima fija para alinear los rows de ambas tablas */
  .list-group-item {
    min-height: 98px;
    display: flex;
    align-items: center;
  }
  
  .rounded-circle {
    flex-shrink: 0;
  }

  .chart-container {
    position: relative;
    height: 300px;
    margin: 10px 0;
  }
  
  .card-chart {
    min-height: 450px;
  }
</style>
@endsection


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
                  $inicial = 'R';
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

    {{-- SECCION DE GRÁFICOS PIE CHARTS --}}
    <div class="row">

      {{-- Universidades --}}
      <div class="col-lg-4">
        <div class="card card-outline card-info shadow-sm card-chart">
          <div class="card-header bg-info text-white">
            <h4 class="card-title mb-0">
              <i class="fas fa-university mr-2"></i>Universidades
            </h4>
          </div>
          <div class="card-body">
            <div class="chart-container">
              <canvas id="chartUniversidades"></canvas>
            </div>
          </div>
        </div>
      </div>

      {{-- Necesidades --}}
      <div class="col-lg-4">
        <div class="card card-outline card-warning shadow-sm card-chart">
          <div class="card-header bg-warning">
            <h4 class="card-title mb-0 text-dark">
              <i class="fas fa-clipboard-list mr-2"></i>Necesidades
            </h4>
          </div>
          <div class="card-body">
            <div class="chart-container">
              <canvas id="chartNecesidades"></canvas>
            </div>
          </div>
        </div>
      </div>

      {{-- Capacitaciones --}}
      <div class="col-lg-4">
        <div class="card card-outline card-success shadow-sm card-chart">
          <div class="card-header bg-success text-white">
            <h4 class="card-title mb-0">
              <i class="fas fa-chalkboard-teacher mr-2"></i>Capacitaciones
            </h4>
          </div>
          <div class="card-body">
            <div class="chart-container">
              <canvas id="chartCapacitaciones"></canvas>
            </div>
          </div>
        </div>
      </div>

    </div>

  </div>
</section>

@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  
  const universidadesData = @json($universidadesData);
  const necesidadesData = @json($necesidadesData);
  const capacitacionesData = @json($capacitacionesData);

  const colorsUniversidades = [
    '#17a2b8', '#20c997', '#ffc107', '#fd7e14', '#e83e8c',
    '#6f42c1', '#007bff', '#28a745', '#dc3545', '#6c757d'
  ];

  const colorsNecesidades = [
    '#ffc107', '#fd7e14', '#e83e8c', '#17a2b8', '#20c997',
    '#6f42c1', '#007bff', '#28a745', '#dc3545', '#6c757d'
  ];

  const colorsCapacitaciones = [
    '#28a745', '#20c997', '#17a2b8', '#007bff', '#6f42c1',
    '#ffc107', '#fd7e14', '#e83e8c', '#dc3545', '#6c757d'
  ];

  const commonOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: 'bottom',
        labels: {
          padding: 15,
          font: {
            size: 11
          }
        }
      },
      tooltip: {
        callbacks: {
          label: function(context) {
            let label = context.label || '';
            if (label) {
              label += ': ';
            }
            label += context.parsed;
            return label;
          }
        }
      }
    }
  };

  // Gráfico Universidades
  if (universidadesData && universidadesData.length > 0) {
    const ctxUniversidades = document.getElementById('chartUniversidades').getContext('2d');
    new Chart(ctxUniversidades, {
      type: 'pie',
      data: {
        labels: universidadesData.map(item => item.label),
        datasets: [{
          data: universidadesData.map(item => item.total),
          backgroundColor: colorsUniversidades.slice(0, universidadesData.length),
          borderWidth: 2,
          borderColor: '#fff'
        }]
      },
      options: commonOptions
    });
  } else {
    document.getElementById('chartUniversidades').parentElement.innerHTML = 
      '<p class="text-center text-muted mt-5">Sin datos disponibles</p>';
  }

  // Gráfico Necesidades
  if (necesidadesData && necesidadesData.length > 0) {
    const ctxNecesidades = document.getElementById('chartNecesidades').getContext('2d');
    new Chart(ctxNecesidades, {
      type: 'pie',
      data: {
        labels: necesidadesData.map(item => item.label),
        datasets: [{
          data: necesidadesData.map(item => item.total),
          backgroundColor: colorsNecesidades.slice(0, necesidadesData.length),
          borderWidth: 2,
          borderColor: '#fff'
        }]
      },
      options: commonOptions
    });
  } else {
    document.getElementById('chartNecesidades').parentElement.innerHTML = 
      '<p class="text-center text-muted mt-5">Sin datos disponibles</p>';
  }

  // Gráfico Capacitaciones
  if (capacitacionesData && capacitacionesData.length > 0) {
    const ctxCapacitaciones = document.getElementById('chartCapacitaciones').getContext('2d');
    new Chart(ctxCapacitaciones, {
      type: 'pie',
      data: {
        labels: capacitacionesData.map(item => item.label),
        datasets: [{
          data: capacitacionesData.map(item => item.total),
          backgroundColor: colorsCapacitaciones.slice(0, capacitacionesData.length),
          borderWidth: 2,
          borderColor: '#fff'
        }]
      },
      options: commonOptions
    });
  } else {
    document.getElementById('chartCapacitaciones').parentElement.innerHTML = 
      '<p class="text-center text-muted mt-5">Sin datos disponibles</p>';
  }

});
</script>
@endsection