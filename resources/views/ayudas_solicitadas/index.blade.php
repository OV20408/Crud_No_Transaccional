@extends('adminlte::page')

@section('title', 'Ayudas Solicitadas')

@section('content')

<style>
  /* Contenedor general tipo React */
  .form-container {
    justify-content: center;
    align-items: flex-start;
    padding: 40px 20px;
    width: 100%;
    min-height: 100vh;
  }

  .form-content {
    padding: 2rem 3rem;
    width: 100%;
    max-width: 1200px;
  }

  .form-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
  }

  .form-titulo {
    font-size: 2.5rem;
    padding-bottom: 0.5rem;
    position: relative;
    margin: 0;
  }
  </style>


<div class="container-fluid">
    <div class="form-container">
        <div class="form-header">
            <h1 class="form-titulo">Ayudas Solicitadas</h1>
            
        </div>
    {{-- 🔶 Barra de búsqueda y filtros --}}
    <div class="card card-dark">
        <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-4 mb-3">
            <label class="mb-1">Busqueda</label>
            <input type="text" class="form-control form-control" placeholder="Buscar por nombre" id="buscarNombre">
            </div>

            <div class="col-md-3 mb-3">
            <label class="mb-1">Prioridad</label>
            <select class="form-control" id="prioridadFiltro">
                <option value="">Todas</option>
                <option value="alto">Alto</option>
                <option value="medio">Medio</option>
                <option value="bajo">Bajo</option>
            </select>
            </div>

            <div class="col-md-3 mb-3">
            <label class="mb-1">Estado</label>
            <select class="form-control" id="estadoFiltro">
                <option value="">Todos</option>
                <option value="sin responder">Sin responder</option>
                <option value="en progreso">En progreso</option>
                <option value="respondido">Respondido</option>
                <option value="resuelto">Resuelto</option>
            </select>
            </div>

            <div class="col-md-2 text-center">
            <button class="btn btn-primary mt-3" id="btnLimpiar">
                <i class="fas fa-times"></i> Limpiar filtros
            </button>
            </div>
        </div>
        </div>
    </div>

    {{-- 🔷 Contenido principal: listado + mapa --}}
    <div class="row mt-4">
        {{-- Columna izquierda: listado --}}
        <div class="col-lg-5 mb-4">
        <div class="card card-outline card-dark">
            <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-list"></i> Lista de Ayudas</h5>
            </div>
            <div class="card-body p-2" style="max-height: 70vh; overflow-y: auto;" id="listado">
            {{-- Tarjetas de ejemplo --}}
            <div class="ayuda-card mb-3">
                <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-1">Bruno Fiorilo</h6>
                <span class="badge badge-prio prio-alto">Alto</span>
                </div>
                <p class="small mb-1 text-muted">Zona Norte - Calle 7</p>
                <p class="small mb-1">Se requiere asistencia médica urgente.</p>
                <p class="small mb-0"><strong>Estado:</strong> En progreso</p>
            </div>

            <div class="ayuda-card mb-3">
                <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-1">Maria Franco</h6>
                <span class="badge badge-prio prio-medio">Medio</span>
                </div>
                <p class="small mb-1 text-muted">Av. Cristo Redentor</p>
                <p class="small mb-1">Falta de insumos en refugio temporal.</p>
                <p class="small mb-0"><strong>Estado:</strong> Respondido</p>
            </div>

            <div class="ayuda-card mb-3">
                <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-1">Carlos García</h6>
                <span class="badge badge-prio prio-bajo">Bajo</span>
                </div>
                <p class="small mb-1 text-muted">Plan 3000 - Calle B</p>
                <p class="small mb-1">Entrega de alimentos en curso.</p>
                <p class="small mb-0"><strong>Estado:</strong> Resuelto</p>
            </div>
            </div>
        </div>

        </div>

        {{-- Columna derecha: mapa --}}
        <div class="col-lg-7">

        <div class="card card-outline card-dark">
            <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Mapa de Ayudas</h5>
            </div>
            <div class="card-body p-0 position-relative">
            <div id="map" style="height:70vh; width:100%;"></div>

            {{-- Leyenda flotante --}}
            <div id="leyenda" class="card shadow-sm p-2"
                style="position:absolute; top:15px; right:15px; z-index:999; background:rgba(255,255,255,.95);">
                <h6 class="text-warning mb-2">Leyenda</h6>
                <div class="d-flex flex-column small">
                <div class="d-flex align-items-center mb-1">
                    <span class="badge-prio prio-alto me-2"></span> Alta
                </div>
                <div class="d-flex align-items-center mb-1">
                    <span class="badge-prio prio-medio me-2"></span> Media
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge-prio prio-bajo me-2"></span> Baja
                </div>
                </div>
            </div>
            </div>
        </div>
        
        </div>
    </div>
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<style>
  .ayuda-card {
    background: #fff;
    border-radius: 12px;
    padding: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,.1);
    transition: transform .2s;
  }
  .ayuda-card:hover { transform: translateY(-3px); box-shadow: 0 4px 10px rgba(0,0,0,.15); }
  .badge-prio { border-radius: 50px; padding: 5px 10px; color: #fff; font-size: .75rem; }
  .prio-alto { background: #d00000; }
  .prio-medio { background: #ffcd00; color: #222; }
  .prio-bajo { background: #1b9e3a; }

  
</style>
@endsection

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const map = L.map('map').setView([-17.806776, -63.15749], 12);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19, attribution: '&copy; OpenStreetMap'
  }).addTo(map);

  // Marcadores de ejemplo
  const markers = [
    { pos: [-17.780, -63.170], color: '#d00000', popup: 'Bruno Fiorilo<br>Prioridad: Alta' },
    { pos: [-17.790, -63.150], color: '#ffcd00', popup: 'Maria Franco<br>Prioridad: Media' },
    { pos: [-17.810, -63.180], color: '#1b9e3a', popup: 'Carlos García<br>Prioridad: Baja' }
  ];
  markers.forEach(m => {
    L.circleMarker(m.pos, {
      radius: 8, color: m.color, weight: 2,
      fillColor: m.color, fillOpacity: 0.9
    }).bindPopup(m.popup).addTo(map);
  });
});
</script>
@endsection




