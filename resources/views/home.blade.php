@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content')

{{-- ENCABEZADO --}}
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
            <h3>12</h3>
            <p>Activos</p>
          </div>
          <div class="icon"><i class="fas fa-users"></i></div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="small-box bg-secondary shadow-sm">
          <div class="inner">
            <h3>9</h3>
            <p>Inactivos</p>
          </div>
          <div class="icon"><i class="fas fa-user-slash"></i></div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="small-box bg-danger shadow-sm">
          <div class="inner">
            <h3>5</h3>
            <p>Alertas recientes</p>
          </div>
          <div class="icon"><i class="fas fa-heartbeat"></i></div>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="small-box bg-success shadow-sm">
          <div class="inner">
            <h3>8</h3>
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
              <li class="list-group-item d-flex align-items-center">
                <div class="rounded-circle bg-primary text-white text-center mr-3" 
                     style="width:40px;height:40px;line-height:40px;font-weight:bold;">A</div>
                <div>
                  <strong>Ana Gómez</strong><br>
                  <small class="text-success font-weight-bold">Activo</small>
                </div>
              </li>

              <li class="list-group-item d-flex align-items-center">
                <div class="rounded-circle bg-primary text-white text-center mr-3" 
                     style="width:40px;height:40px;line-height:40px;font-weight:bold;">B</div>
                <div>
                  <strong>Bruno Pérez</strong><br>
                  <small class="text-danger font-weight-bold">Inactivo</small>
                </div>
              </li>

              <li class="list-group-item d-flex align-items-center">
                <div class="rounded-circle bg-primary text-white text-center mr-3" 
                     style="width:40px;height:40px;line-height:40px;font-weight:bold;">C</div>
                <div>
                  <strong>Carla Torres</strong><br>
                  <small class="text-success font-weight-bold">Activo</small>
                </div>
              </li>
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

              <li class="list-group-item d-flex align-items-center">
                <div class="rounded-circle bg-danger text-white text-center mr-3" 
                     style="width:40px;height:40px;line-height:40px;font-weight:bold;">L</div>
                <div>
                  <strong>Luis Romero</strong><br>
                  <small class="text-danger font-weight-bold">Crítico</small>
                </div>
              </li>

              <li class="list-group-item d-flex align-items-center">
                <div class="rounded-circle bg-danger text-white text-center mr-3" 
                     style="width:40px;height:40px;line-height:40px;font-weight:bold;">M</div>
                <div>
                  <strong>María López</strong><br>
                  <small class="text-warning font-weight-bold">Pendiente</small>
                </div>
              </li>

              <li class="list-group-item d-flex align-items-center">
                <div class="rounded-circle bg-danger text-white text-center mr-3" 
                     style="width:40px;height:40px;line-height:40px;font-weight:bold;">D</div>
                <div>
                  <strong>Diego Suárez</strong><br>
                  <small class="text-success font-weight-bold">Resuelto</small>
                </div>
              </li>

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
            <h4 class="card-title mb-0"><i class="fas fa-university mr-2"></i>Universidades</h4>
          </div>
          <div class="card-body">
            <ul class="list-unstyled mb-0">
              <li><i class="fas fa-circle text-info mr-2"></i> Universidad Univalle</li>
              <li><i class="fas fa-circle text-info mr-2"></i> Universidad UPSA</li>
              <li><i class="fas fa-circle text-info mr-2"></i> Universidad Católica</li>
            </ul>
          </div>
        </div>
      </div>

      {{-- Necesidades --}}
      <div class="col-lg-4">
        <div class="card card-outline card-warning shadow-sm">
          <div class="card-header bg-warning">
            <h4 class="card-title mb-0 text-dark"><i class="fas fa-clipboard-list mr-2"></i>Necesidades</h4>
          </div>
          <div class="card-body">
            <ul class="list-unstyled mb-0">
              <li><i class="fas fa-circle text-warning mr-2"></i> Alimentos básicos</li>
              <li><i class="fas fa-circle text-warning mr-2"></i> Ropa de abrigo</li>
              <li><i class="fas fa-circle text-warning mr-2"></i> Material educativo</li>
            </ul>
          </div>
        </div>
      </div>

      {{-- Capacitaciones --}}
      <div class="col-lg-4">
        <div class="card card-outline card-success shadow-sm">
          <div class="card-header bg-success text-white">
            <h4 class="card-title mb-0"><i class="fas fa-chalkboard-teacher mr-2"></i>Capacitaciones</h4>
          </div>
          <div class="card-body">
            <ul class="list-unstyled mb-0">
              <li><i class="fas fa-circle text-success mr-2"></i> Primeros auxilios</li>
              <li><i class="fas fa-circle text-success mr-2"></i> Seguridad comunitaria</li>
              <li><i class="fas fa-circle text-success mr-2"></i> Logística en emergencias</li>
            </ul>
          </div>
        </div>
      </div>

    </div>


  </div>
</section>

@endsection
