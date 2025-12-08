@extends('adminlte::page')

@section('title', 'Ayudas Solicitadas')

@section('content')

<style>
  .form-container {
    justify-content: center;
    align-items: flex-start;
    padding: 40px 20px;
    width: 100%;
    min-height: 100vh;
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

  .ayuda-card {
    background: #fff;
    border-radius: 12px;
    padding: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,.1);
    transition: transform .2s;
    cursor: pointer;
  }
  .ayuda-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 10px rgba(0,0,0,.15);
  }
  .badge-prio {
    border-radius: 50px;
    padding: 5px 10px;
    color: #fff;
    font-size: .75rem;
  }
  .prio-alto { background: #d00000; }
  .prio-medio { background: #ffcd00; color: #222; }
  .prio-bajo { background: #1b9e3a; }

  .mensaje-vacio {
    font-size: .9rem;
    color: #777;
    text-align: center;
    padding: 1rem 0;
  }
</style>

<div class="container-fluid">
  <div class="form-container">
    <div class="form-header">
      <h1 class="form-titulo">Ayudas Solicitadas</h1>
    </div>

    {{-- Barra de búsqueda y filtros --}}
    <div class="card card-dark">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-md-4 mb-3">
            <label class="mb-1">Búsqueda</label>
            <input type="text" class="form-control" placeholder="Buscar por nombre" id="buscarNombre">
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

    {{-- Contenido principal: listado + mapa --}}
    <div class="row mt-4">
      {{-- Columna izquierda: listado --}}
      <div class="col-lg-5 mb-4">
        <div class="card card-outline card-dark">
          <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-list"></i> Lista de Ayudas</h5>
          </div>
          <div class="card-body p-2" style="max-height: 70vh; overflow-y: auto;" id="listado">
            {{-- aquí JS inyecta las cards --}}
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
@endsection

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
  document.addEventListener('DOMContentLoaded', async function () {
    let datos = [];

    const buscarNombre   = document.getElementById('buscarNombre');
    const prioridadFiltro= document.getElementById('prioridadFiltro');
    const estadoFiltro   = document.getElementById('estadoFiltro');
    const btnLimpiar     = document.getElementById('btnLimpiar');
    const listadoDiv     = document.getElementById('listado');

    // Función para cargar datos frescos desde la base de datos
    async function cargarDatosFrescos() {
      try {
        const response = await fetch('/ayudas_solicitadas');
        const html = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const scriptContent = doc.querySelector('script:not([src])').textContent;
        const match = scriptContent.match(/const datos = (.+?);/);
        if (match) {
          datos = JSON.parse(match[1]);
          aplicarFiltros();
        }
      } catch (error) {
        console.error('Error recargando datos:', error);
      }
    }

    // Cargar inicial
    datos = {!! $solicitudesJson !!};

    // --- Mapa Leaflet ---
    const map = L.map('map').setView([-17.806776, -63.15749], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    const markersLayer = L.layerGroup().addTo(map);

    function colorPorPrioridad(prio) {
      prio = (prio || '').toLowerCase();
      if (prio === 'alto') return '#d00000';
      if (prio === 'medio') return '#ffcd00';
      if (prio === 'bajo') return '#1b9e3a';
      return '#6c757d';
    }

    function crearIcono(prioridad) {
      const color = colorPorPrioridad(prioridad);
      return L.divIcon({
        html: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="${color}" width="28" height="28">
                 <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
               </svg>`,
        className: '',
        iconSize: [28, 28],
        iconAnchor: [14, 28],
      });
    }

    let marcadoresPorId = {};

    function renderMapa(lista) {
      markersLayer.clearLayers();
      marcadoresPorId = {};

      if (lista.length === 0) return;

      const bounds = [];

      lista.forEach(item => {
        if (item.latitud == null || item.longitud == null) return;

        const pos = [item.latitud, item.longitud];
        bounds.push(pos);

        const marker = L.marker(pos, { icon: crearIcono(item.prioridad) })
          .bindPopup(`
            <div>
              <strong>${item.voluntario}</strong><br>
              ${item.direccion ?? ''}<br>
              <strong>Prioridad:</strong> <span style="color:${colorPorPrioridad(item.prioridad)}">${item.prioridad}</span><br>
              <strong>Estado:</strong> ${item.estado}<br>
              <strong>Detalle:</strong> ${item.detalle ?? ''}<br>
              <strong>Fecha:</strong> ${item.fecha ?? ''}
            </div>
          `)
          .addTo(markersLayer);

        marcadoresPorId[item.id] = marker;
      });

      if (bounds.length > 0) {
        map.flyToBounds(bounds, { padding: [50, 50] });
      }
    }

    function claseBadge(prioridad) {
      const p = (prioridad || '').toLowerCase();
      if (p === 'alto') return 'badge-prio prio-alto';
      if (p === 'medio') return 'badge-prio prio-medio';
      if (p === 'bajo') return 'badge-prio prio-bajo';
      return 'badge badge-secondary';
    }

    function renderListado(lista) {
      listadoDiv.innerHTML = '';

      if (lista.length === 0) {
        listadoDiv.innerHTML = '<p class="mensaje-vacio">No se encontraron resultados.</p>';
        return;
      }

      lista.forEach(item => {
        const card = document.createElement('div');
        card.className = 'ayuda-card mb-3';
        
        let botonAccion = '';
        
        switch(item.estado ? item.estado.toLowerCase() : 'sin responder') {
          case 'sin responder':
            botonAccion = `
              <a href="/chat-consulta?emergencia=1&voluntario_id=${item.voluntario_id}&ayuda_id=${item.id}" 
                 class="btn btn-sm btn-danger w-100">
                 <i class="fas fa-exclamation-triangle"></i> Atender emergencia
              </a>`;
            break;
            
          case 'en progreso':
            botonAccion = `
              <a href="/chat-consulta?emergencia=1&voluntario_id=${item.voluntario_id}&ayuda_id=${item.id}" 
                 class="btn btn-sm btn-warning w-100">
                 <i class="fas fa-comments"></i> Continuar chat
              </a>`;
            break;
            
          case 'respondido':
          case 'resuelto':
            botonAccion = `
              <a href="/chat-consulta?emergencia=1&voluntario_id=${item.voluntario_id}&ayuda_id=${item.id}" 
                 class="btn btn-sm btn-success w-100">
                 <i class="fas fa-check-circle"></i> Ver resolución
              </a>`;
            break;
        }

        card.innerHTML = `
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">${item.voluntario || 'Sin nombre'}</h6>
            <span class="${claseBadge(item.prioridad)}">${(item.prioridad || 'media').toUpperCase()}</span>
          </div>
          <p class="small mb-1 text-muted">
            <i class="fas fa-map-marker-alt"></i> ${item.direccion || 'Ubicación reportada'}
          </p>
          <p class="small mb-2">${item.detalle || 'Sin descripción'}</p>
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="badge badge-${getBadgeColor(item.estado)}">
              ${(item.estado || 'sin responder').toUpperCase()}
            </span>
            <small class="text-muted">
              <i class="far fa-clock"></i> ${item.fecha || ''}
            </small>
          </div>
          
          <div class="mt-2">
            ${botonAccion}
          </div>
        `;

        card.addEventListener('click', (e) => {
          if (!e.target.closest('a.btn')) {
            const marker = marcadoresPorId[item.id];
            if (marker) {
              map.flyTo(marker.getLatLng(), 15, { duration: 0.5 });
              marker.openPopup();
            }
          }
        });

        listadoDiv.appendChild(card);
      });
    }

    function getBadgeColor(estado) {
      const colores = {
        'sin responder': 'danger',
        'en progreso': 'warning',
        'respondido': 'success',
        'resuelto': 'primary'
      };
      return colores[(estado || '').toLowerCase()] || 'secondary';
    }

    function aplicarFiltros() {
      const texto = buscarNombre.value.trim().toLowerCase();
      const prio = prioridadFiltro.value.toLowerCase();
      const est  = estadoFiltro.value.toLowerCase();

      const filtradas = datos.filter(item => {
        const nombreOk =
          texto === '' ||
          (item.voluntario || '').toLowerCase().includes(texto);

        const prioOk =
          prio === '' ||
          (item.prioridad || '').toLowerCase() === prio;

        const estOk =
          est === '' ||
          (item.estado || '').toLowerCase() === est;

        return nombreOk && prioOk && estOk;
      });

      renderListado(filtradas);
      renderMapa(filtradas);
    }

    buscarNombre.addEventListener('input', aplicarFiltros);
    prioridadFiltro.addEventListener('change', aplicarFiltros);
    estadoFiltro.addEventListener('change', aplicarFiltros);

    btnLimpiar.addEventListener('click', function () {
      buscarNombre.value = '';
      prioridadFiltro.value = '';
      estadoFiltro.value = '';
      aplicarFiltros();
    });

    // Recargar datos cuando la ventana recupera el foco (vienes del chat)
    window.addEventListener('focus', cargarDatosFrescos);

    // Render inicial
    aplicarFiltros();
  });
</script>
@endsection