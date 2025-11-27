@extends('adminlte::page')

@section('content')
<style>
  :root {
    --color-amarillo: #FFA726;
    --color-card: #ffffff;
    --color-texto-principal: #333333;
    --color-blanco: #f8f9fa;
  }

  .infovoluntarios-container {
    padding: 20px;
    width: 100%;
    min-height: 100vh;
  }

  .infovoluntarios-header {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    margin-bottom: 30px;
    background: var(--color-card);
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
  }

  .info-avatar {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--color-amarillo) 0%, #FFB74D 100%);
    color: white;
    font-size: 32px;
    font-weight: bold;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(255, 167, 38, 0.3);
  }

  .nombre-voluntario {
    font-size: 2rem;
    color: var(--color-texto-principal);
    margin: 0 0 5px 0;
    font-weight: bold;
  }

  .email-voluntario {
    color: #666;
    margin: 0 0 10px 0;
    font-size: 1rem;
  }

  .header-status-group {
    display: flex;
    gap: 15px;
    align-items: center;
    flex-wrap: wrap;
  }

  .estado-info {
    padding: 6px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .estado-info.activo {
    background-color: rgba(46, 125, 50, 0.1);
    color: #2e7d32;
  }

  .estado-info.inactivo {
    background-color: rgba(198, 40, 40, 0.1);
    color: #c62828;
  }

  .estado-info .dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: currentColor;
  }

  .btn-formulario-enviar,
  .btn-descargar-pdf {
    padding: 8px 16px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
  }

  .btn-formulario-enviar {
    background-color: var(--color-amarillo);
    color: white;
  }

  .btn-formulario-enviar:hover {
    background-color: #FB8C00;
    transform: translateY(-2px);
  }

  .btn-descargar-pdf {
    background-color: #1976D2;
    color: white;
    text-decoration: none;
    display: inline-block;
  }

  .btn-descargar-pdf:hover {
    background-color: #1565C0;
    transform: translateY(-2px);
    text-decoration: none;
    color: white;
  }

  .infovoluntarios-paneles {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
  }

  .panel-hover {
    background: var(--color-card);
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .panel-hover:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
  }

  .panel-hover h4 {
    color: var(--color-amarillo);
    font-size: 1.2rem;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: bold;
  }

  .panel-hover p,
  .panel-hover .item-evaluacion {
    margin: 10px 0;
    color: var(--color-texto-principal);
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .no-evaluacion {
    text-align: center;
    padding: 30px;
    color: #999;
  }

  .no-evaluacion .icono-vacio {
    font-size: 3rem;
    color: #ddd;
    margin-bottom: 10px;
  }

  .alternar-vista {
    margin: 30px 0;
  }

  .opciones-boton {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
  }

  .btn-opcion {
    padding: 12px 20px;
    background: var(--color-card);
    border: 2px solid var(--color-amarillo);
    color: var(--color-amarillo);
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .btn-opcion:hover {
    background: var(--color-amarillo);
    color: white;
    transform: translateY(-2px);
  }

  .vistas {
    background: var(--color-card);
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
    min-height: 300px;
  }

  .titulo-seccion {
    color: var(--color-amarillo);
    font-size: 1.8rem;
    margin-bottom: 20px;
    font-weight: bold;
  }

  .vista-card {
    background: var(--color-blanco);
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid var(--color-amarillo);
    margin-bottom: 15px;
    transition: all 0.3s ease;
  }

  .vista-card:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  }

  .vista-card strong {
    color: var(--color-texto-principal);
    font-size: 1.1rem;
    display: block;
    margin-bottom: 8px;
  }

  .vista-card p {
    color: #666;
    margin: 5px 0;
  }

  .mensaje-vacio {
    text-align: center;
    color: #999;
    font-style: italic;
    padding: 40px;
  }

  .historial-toggle {
    background: linear-gradient(135deg, var(--color-amarillo) 0%, #FFB74D 100%);
    padding: 15px 20px;
    border-radius: 8px;
    cursor: pointer;
    margin-bottom: 15px;
    transition: all 0.3s ease;
  }

  .historial-toggle:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 167, 38, 0.3);
  }

  .historial-toggle h4 {
    color: white;
    margin: 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: bold;
  }

  .flecha-historial {
    font-size: 1.2rem;
  }

  .historial-seccion {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.5s ease;
  }

  .historial-seccion.visible {
    max-height: 2000px;
    margin-bottom: 20px;
  }

  .btn-volver {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: var(--color-amarillo);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    margin-bottom: 20px;
  }

  .btn-volver:hover {
    background: #FB8C00;
    transform: translateY(-2px);
    text-decoration: none;
    color: white;
  }

  @media (max-width: 768px) {
    .infovoluntarios-header {
      flex-direction: column;
    }

    .nombre-voluntario {
      font-size: 1.5rem;
    }

    .opciones-boton {
      flex-direction: column;
    }

    .btn-opcion {
      width: 100%;
    }
  }
</style>

<div class="infovoluntarios-container">
  <a href="{{ route('voluntarios.index') }}" class="btn-volver">
    <i class="fas fa-arrow-left"></i> Volver a la lista
  </a>

  {{-- Header con información del voluntario --}}
  <header class="infovoluntarios-header">
    <div class="info-avatar">
      <span>{{ strtoupper(substr($voluntario->nombres, 0, 1)) }}</span>
    </div>
    <div style="flex: 1;">
      <h1 class="nombre-voluntario">{{ $voluntario->nombres }} {{ $voluntario->apellidos }}</h1>
      <p class="email-voluntario">{{ $voluntario->email ?? 'Sin email' }}</p>
      <div class="header-status-group">
        <div class="estado-info {{ strtolower($voluntario->estado) }}">
          <span class="dot"></span>
          {{ ucfirst($voluntario->estado) }}
        </div>
        <button class="btn-formulario-enviar" onclick="alert('Funcionalidad de enviar formulario')">
          Enviar Formulario
        </button>
        @if(count($reportes) > 0)
          <a href="#" class="btn-descargar-pdf">
            Descargar Historial Clínico
          </a>
        @endif
      </div>
    </div>
  </header>

  {{-- Paneles de información --}}
  <section class="infovoluntarios-paneles">
    {{-- Datos Personales --}}
    <div class="panel-hover panel-personal">
      <h4>
        <i class="fas fa-id-card"></i>
        Datos Personales
      </h4>
      <p><i class="fas fa-calendar-alt"></i> {{ $voluntario->fecha_nacimiento ?? 'N/D' }}</p>
      <p><i class="fas fa-venus-mars"></i> {{ $voluntario->genero ?? 'N/D' }}</p>
      <p><i class="fas fa-phone"></i> {{ $voluntario->telefono ?? 'N/D' }}</p>
      <p><i class="fas fa-tint"></i> {{ $voluntario->tipo_sangre ?? 'N/D' }}</p>
      <p><i class="fas fa-map-marker-alt"></i> {{ $voluntario->direccion_domicilio ?? 'N/D' }}</p>
      <p><i class="fas fa-id-card"></i> {{ $voluntario->ci }}</p>
    </div>

    {{-- Evaluaciones Físicas --}}
    <div class="panel-hover panel-fisico">
      <h4>
        <i class="fas fa-heartbeat"></i>
        Evaluaciones Físicas
      </h4>
      @if($reporteMasReciente && $reporteMasReciente->resumen_fisico)
        <div class="item-evaluacion">
          <i class="fas fa-file-alt"></i>
          <span>Última evaluación: {{ \Carbon\Carbon::parse($reporteMasReciente->fecha_generado)->format('d/m/Y') }}</span>
        </div>
        <div class="item-evaluacion">
          <i class="fas fa-chart-line"></i>
          <span>Reporte #{{ $reporteMasReciente->id }}</span>
        </div>
        <p>{{ $reporteMasReciente->resumen_fisico }}</p>
      @else
        <div class="no-evaluacion">
          <i class="fas fa-file-alt icono-vacio"></i>
          <p>No hay evaluaciones físicas registradas.</p>
        </div>
      @endif
    </div>

    {{-- Evaluaciones Psicológicas --}}
    <div class="panel-hover panel-psicologico">
      <h4>
        <i class="fas fa-brain"></i>
        Evaluaciones Psicológicas
      </h4>
      @if($reporteMasReciente && $reporteMasReciente->resumen_emocional)
        <div class="item-evaluacion">
          <i class="fas fa-file-alt"></i>
          <span>Última evaluación: {{ \Carbon\Carbon::parse($reporteMasReciente->fecha_generado)->format('d/m/Y') }}</span>
        </div>
        <div class="item-evaluacion">
          <i class="fas fa-chart-line"></i>
          <span>Reporte #{{ $reporteMasReciente->id }}</span>
        </div>
        <p>{{ $reporteMasReciente->resumen_emocional }}</p>
      @else
        <div class="no-evaluacion">
          <i class="fas fa-file-alt icono-vacio"></i>
          <p>No hay evaluaciones psicológicas registradas.</p>
        </div>
      @endif
    </div>
  </section>

  {{-- Botones de vistas --}}
  <section class="alternar-vista">
    <div class="opciones-boton">
      <button class="btn-opcion" onclick="mostrarVista('historial')">
        <i class="fas fa-history"></i> Historial
      </button>
      <button class="btn-opcion" onclick="mostrarVista('reportes')">
        <i class="fas fa-file-medical"></i> Reportes
      </button>
      <button class="btn-opcion" onclick="mostrarVista('capacitaciones')">
        <i class="fas fa-certificate"></i> Capacitaciones
      </button>
      <button class="btn-opcion" onclick="mostrarVista('encuestas')">
        <i class="fas fa-poll"></i> Encuestas
      </button>
      <button class="btn-opcion" onclick="mostrarVista('cursos')">
        <i class="fas fa-book"></i> Cursos
      </button>
    </div>
  </section>

  {{-- Área de vistas --}}
  <section class="vistas">
    <div id="vista-contenido">
      <p class="mensaje-vacio">Selecciona una opción para ver el contenido</p>
    </div>
  </section>
</div>

<script>
  function mostrarVista(vista) {
    const contenido = document.getElementById('vista-contenido');
    
    switch(vista) {
      case 'historial':
        contenido.innerHTML = `
          <h2 class="titulo-seccion">Historial</h2>
          @if(count($reportes) > 0)
            <div class="panel-historial">
              <div class="historial-toggle" onclick="toggleHistorial('clinico')">
                <h4>
                  Clínico
                  <i class="fas fa-chevron-down flecha-historial" id="flecha-clinico"></i>
                </h4>
              </div>
              <div class="historial-seccion" id="seccion-clinico">
                @foreach($reportes as $reporte)
                  @if($reporte->resumen_fisico)
                    <div class="vista-card">
                      <p>{{ $reporte->resumen_fisico }}</p>
                      <small>{{ \Carbon\Carbon::parse($reporte->fecha_generado)->format('d/m/Y') }}</small>
                    </div>
                  @endif
                @endforeach
              </div>

              <div class="historial-toggle" onclick="toggleHistorial('psicologico')">
                <h4>
                  Psicológico
                  <i class="fas fa-chevron-down flecha-historial" id="flecha-psicologico"></i>
                </h4>
              </div>
              <div class="historial-seccion" id="seccion-psicologico">
                @foreach($reportes as $reporte)
                  @if($reporte->resumen_emocional)
                    <div class="vista-card">
                      <p>{{ $reporte->resumen_emocional }}</p>
                      <small>{{ \Carbon\Carbon::parse($reporte->fecha_generado)->format('d/m/Y') }}</small>
                    </div>
                  @endif
                @endforeach
              </div>
            </div>
          @else
            <p class="mensaje-vacio">No hay historial disponible.</p>
          @endif
        `;
        break;

      case 'reportes':
        contenido.innerHTML = `
          <h2 class="titulo-seccion">Reportes</h2>
          @if(count($reportes) > 0)
            @foreach($reportes as $reporte)
              <div class="vista-card">
                <strong>Reporte #{{ $reporte->id }}</strong>
                <p><strong>Estado:</strong> {{ $reporte->estado_general ?? 'N/D' }}</p>
                <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($reporte->fecha_generado)->format('d/m/Y H:i') }}</p>
                @if($reporte->observaciones)
                  <p><strong>Observaciones:</strong> {{ $reporte->observaciones }}</p>
                @endif
              </div>
            @endforeach
          @else
            <p class="mensaje-vacio">No hay reportes disponibles.</p>
          @endif
        `;
        break;

      case 'capacitaciones':
        contenido.innerHTML = `
          <h2 class="titulo-seccion">Capacitaciones y Certificaciones</h2>
          @if(count($capacitaciones) > 0)
            @foreach($capacitaciones as $cap)
              <div class="vista-card">
                <strong>{{ $cap->nombre }}</strong>
                <p>{{ $cap->descripcion }}</p>
              </div>
            @endforeach
          @else
            <p class="mensaje-vacio">No hay capacitaciones asignadas.</p>
          @endif
        `;
        break;

      case 'encuestas':
        contenido.innerHTML = `
          <h2 class="titulo-seccion">Encuestas Realizadas</h2>
          @if(count($evaluaciones) > 0)
            @foreach($evaluaciones as $evaluacion)
              <div class="vista-card">
                <strong>{{ $evaluacion->test_nombre }}</strong>
                <p>Fecha: {{ \Carbon\Carbon::parse($evaluacion->fecha)->format('d/m/Y') }}</p>
                <p>Reporte #{{ $evaluacion->id_reporte }}</p>
              </div>
            @endforeach
          @else
            <p class="mensaje-vacio">No hay encuestas realizadas.</p>
          @endif
        `;
        break;

      case 'cursos':
        contenido.innerHTML = `
          <h2 class="titulo-seccion">Cursos</h2>
          @if(count($cursos) > 0)
            @foreach($cursos as $curso)
              <div class="vista-card">
                <strong>{{ $curso->nombre }}</strong>
                <p>{{ $curso->descripcion }}</p>
                <p><em>Capacitación: {{ $curso->capacitacion_nombre }}</em></p>
              </div>
            @endforeach
          @else
            <p class="mensaje-vacio">No hay cursos asignados.</p>
          @endif
        `;
        break;
    }
  }

  function toggleHistorial(tipo) {
    const seccion = document.getElementById('seccion-' + tipo);
    const flecha = document.getElementById('flecha-' + tipo);
    
    if (seccion.classList.contains('visible')) {
      seccion.classList.remove('visible');
      flecha.classList.remove('fa-chevron-up');
      flecha.classList.add('fa-chevron-down');
    } else {
      seccion.classList.add('visible');
      flecha.classList.remove('fa-chevron-down');
      flecha.classList.add('fa-chevron-up');
    }
  }
</script>
@endsection