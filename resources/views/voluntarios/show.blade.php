@extends('adminlte::page')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
  :root {
    --color-amarillo: #FFA726;
    --color-card: #ffffff;
    --color-texto-principal: #333333;
    --color-blanco: #f8f9fa;
    --color-azul: #007bff;
    --color-gris: #6c757d;
  }

  /* Toast Notification Styles */
  .toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
  }

  .toast-notification {
    display: none;
    min-width: 320px;
    padding: 16px 20px;
    border-radius: 8px;
    border: 1px solid;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    margin-bottom: 10px;
    animation: slideIn 0.3s ease;
  }

  @keyframes slideIn {
    from {
      transform: translateX(100%);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }

  @keyframes slideOut {
    from {
      transform: translateX(0);
      opacity: 1;
    }
    to {
      transform: translateX(100%);
      opacity: 0;
    }
  }

  .toast-notification.toast-success {
    background-color: #f8f9fa;
    border-color: #6c757d;
    color: #333333;
  }

  .toast-notification.toast-error {
    background-color: #fff5f5;
    border-color: #dc3545;
    color: #721c24;
  }

  .toast-notification.toast-loading {
    background-color: #f8f9fa;
    border-color: #6c757d;
    color: #333333;
  }

  .toast-content {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .toast-icon {
    font-size: 24px;
    flex-shrink: 0;
  }

  .toast-message {
    flex: 1;
  }

  .toast-message h4 {
    margin: 0 0 4px 0;
    font-size: 16px;
    font-weight: 600;
  }

  .toast-message p {
    margin: 0;
    font-size: 14px;
    opacity: 0.9;
  }

  .toast-close {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    opacity: 0.6;
    padding: 0;
    line-height: 1;
  }

  .toast-close:hover {
    opacity: 1;
  }

  .spinner {
    width: 24px;
    height: 24px;
    border: 3px solid #6c757d;
    border-top-color: transparent;
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
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
    background: linear-gradient(135deg, var(--color-azul) 0%, #419dff 100%);
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
    background-color: var(--color-azul);
    color: white;
  }

  .btn-formulario-enviar:hover {
    transform: none;
    background-color: var(--color-azul);
    text-decoration: none;
    color: white;
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
    color: var(--color-azul);
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
    border: 2px solid var(--color-azul);
    color: var(--color-azul);
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .btn-opcion:hover {
    background: var(--color-azul);
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
    color: var(--color-azul);
    font-size: 1.8rem;
    margin-bottom: 20px;
    font-weight: bold;
  }

  .vista-card {
    background: var(--color-blanco);
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid var(--color-azul);
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
    background: linear-gradient(135deg, var(--color-azul) 0%, #0056b3 100%);
    padding: 15px 20px;
    border-radius: 8px;
    cursor: pointer;
    margin-bottom: 15px;
    transition: all 0.3s ease;
  }

  .historial-toggle:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
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
    transition: transform 0.3s ease;
  }

  .flecha-historial.rotated {
    transform: rotate(180deg);
  }

  .historial-seccion {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.5s ease;
  }

  .historial-seccion.visible {
    max-height: 5000px;
    margin-bottom: 20px;
  }

  /* Historial Tabla 2 columnas */
  .historial-tabla {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-top: 15px;
  }

  .historial-columna {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .historial-columna-header {
    background: linear-gradient(135deg, var(--color-azul) 0%, #0056b3 100%);
    color: white;
    padding: 12px 15px;
    border-radius: 8px;
    font-weight: bold;
    text-align: center;
    font-size: 1rem;
  }

  .historial-columna-header.psicologico {
    background: linear-gradient(135deg, var(--color-azul) 0%, #0056b3 100%);
  }

  .historial-item {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    border-left: 4px solid var(--color-azul);
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .historial-item.psicologico {
    border-left-color: var(--color-azul);
  }

  .historial-item-content {
    font-size: 0.9rem;
    color: #333;
    line-height: 1.5;
  }

  .historial-item-fecha {
    font-size: 0.8rem;
    color: #666;
    font-weight: 500;
    text-align: right;
  }

  @media (max-width: 992px) {
    .historial-tabla {
      grid-template-columns: 1fr;
    }
  }

  .btn-volver {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: var(--color-azul);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    margin-bottom: 20px;
  }

  .btn-volver:hover {
    background: var(--color-azul);
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
  <!-- Toast Container -->
  <div class="toast-container">
    <div class="toast-notification toast-loading" id="toast-loading">
      <div class="toast-content">
        <div class="spinner"></div>
        <div class="toast-message">
          <h4>Enviando formulario...</h4>
          <p>Por favor espere</p>
        </div>
      </div>
    </div>
    <div class="toast-notification toast-success" id="toast-success">
      <div class="toast-content">
        <span class="toast-icon">✓</span>
        <div class="toast-message">
          <h4>¡Formulario enviado!</h4>
          <p id="toast-success-msg">El correo ha sido enviado correctamente</p>
        </div>
        <button class="toast-close" onclick="hideToast('toast-success')">&times;</button>
      </div>
    </div>
    <div class="toast-notification toast-error" id="toast-error">
      <div class="toast-content">
        <span class="toast-icon">✕</span>
        <div class="toast-message">
          <h4>Error</h4>
          <p id="toast-error-msg">No se pudo enviar el formulario</p>
        </div>
        <button class="toast-close" onclick="hideToast('toast-error')">&times;</button>
      </div>
    </div>
  </div>

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
        <button class="btn-formulario-enviar" onclick="enviarFormularioVoluntario({{ $voluntario->id_usuario }})" id="btn-enviar-formulario">
          <i class="fas fa-paper-plane"></i> Enviar Formulario
        </button>
        <a href="{{ route('voluntarios.historial.pdf', $voluntario->id_usuario) }}?v={{ time() }}" 
          class="btn-formulario-enviar" 
          style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-file-pdf"></i> Descargar Historial Clínico
        </a>
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
      <p><i class="fas fa-calendar-alt"></i> {{ $voluntario->fecha_nacimiento ? \Carbon\Carbon::parse($voluntario->fecha_nacimiento)->format('d/m/Y') : 'N/D' }}</p>
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
        <i class="fas fa-poll"></i> Encuestas Realizadas
      </button>
      <button class="btn-opcion" onclick="mostrarVista('cursos')">
        <i class="fas fa-book"></i> Cursos del Voluntario
      </button>
      <button class="btn-opcion" onclick="mostrarVista('necesidades')">
        <i class="fas fa-book"></i> Analisis de Necesidades
      </button>
    </div>
  </section>

  {{-- Área de vistas --}}
  <section class="vistas">
    <div id="vista-contenido">
      <p class="mensaje-vacio">Selecciona una opción para ver el contenido</p>
    </div>
  </section>

  {{-- Modal para asignar capacitación --}}
<div class="modal fade" id="modalAsignarCapacitacion" tabindex="-1" role="dialog" aria-labelledby="modalAsignarCapacitacionLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="POST" action="{{ route('voluntarios.capacitaciones.asignar', $voluntario->id_usuario) }}" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="modalAsignarCapacitacionLabel">Asignar capacitación</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="form-group">
          <label for="capacitacion_id">Capacitación</label>
          <select name="capacitacion_id" id="capacitacion_id" class="form-control" required>
            <option value="">-- Selecciona una capacitación --</option>
            @foreach($capacitacionesAll as $cap)
              <option value="{{ $cap->id }}">{{ $cap->nombre }}</option>
            @endforeach
          </select>
        </div>

        @if($errors->any())
          <div class="alert alert-danger mt-2">
            {{ $errors->first() }}
          </div>
        @endif
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Asignar</button>
      </div>
    </form>
  </div>
</div>


{{-- Modal para asignar necesidad --}}
<div class="modal fade" id="modalAsignarNecesidad" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form method="POST" action="{{ route('voluntarios.necesidades.asignar', $voluntario->id_usuario) }}" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Asignar Necesidad</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="form-group">
          <label for="necesidad_id">Necesidad</label>
          <select name="necesidad_id" id="necesidad_id" class="form-control" required>
            <option value="">-- Selecciona una necesidad --</option>
            @foreach($necesidadesAll as $nec)
              <option value="{{ $nec->id }}">{{ $nec->tipo }} - {{ $nec->descripcion }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Asignar</button>
      </div>
    </form>
  </div>
</div>



</div>

<script>
  function mostrarVista(vista) {
    const contenido = document.getElementById('vista-contenido');
    
    switch(vista) {
      case 'historial':
        contenido.innerHTML = `
          <h2 class="titulo-seccion">Historial</h2>
          @if(count($reportes) > 0)
            <div class="historial-tabla">
              <!-- Columna Clínico -->
              <div class="historial-columna">
                <div class="historial-columna-header">
                  <i class="fas fa-heartbeat mr-2"></i> Clínico
                </div>
                @foreach($reportes as $reporte)
                  @if($reporte->resumen_fisico)
                    <div class="historial-item">
                      <div class="historial-item-content">{{ $reporte->resumen_fisico }}</div>
                      <div class="historial-item-fecha">{{ \Carbon\Carbon::parse($reporte->fecha_generado)->format('d/m/Y') }}</div>
                    </div>
                  @endif
                @endforeach
              </div>

              <!-- Columna Psicológico -->
              <div class="historial-columna">
                <div class="historial-columna-header psicologico">
                  <i class="fas fa-brain mr-2"></i> Psicológico
                </div>
                @foreach($reportes as $reporte)
                  @if($reporte->resumen_emocional)
                    <div class="historial-item psicologico">
                      <div class="historial-item-content">{{ $reporte->resumen_emocional }}</div>
                      <div class="historial-item-fecha">{{ \Carbon\Carbon::parse($reporte->fecha_generado)->format('d/m/Y') }}</div>
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
    <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;">
      <h2 class="titulo-seccion" style="margin-bottom:0;">Capacitaciones y Certificaciones</h2>
      <button class="btn-formulario-enviar" data-toggle="modal" data-target="#modalAsignarCapacitacion">
        <i class="fas fa-plus-circle"></i> Asignar capacitación
      </button>
    </div>

    <div style="margin-top:20px;">
    @if(count($capacitacionesProgreso) > 0)
      <div class="row">
        @foreach($capacitacionesProgreso as $cap)
          <div class="col-md-6 mb-3">
            <div class="vista-card curso-card" 
                 style="cursor:pointer;transition:all 0.3s;" 
                 onclick="toggleCursoDetalles({{ $cap->id }})">
              
              <div style="display:flex;justify-content:space-between;align-items:start;">
                <div>
                  <strong style="font-size:1.2rem;">{{ $cap->nombre }}</strong>
                  <p style="margin:5px 0;color:#666;">{{ $cap->descripcion }}</p>
                </div>
                <i id="icono-curso-{{ $cap->id }}" class="fas fa-chevron-down" style="transition:transform 0.3s;"></i>
              </div>

              {{-- 🔹 DETALLES DEL CURSO (inicialmente ocultos) --}}
              <div id="detalles-curso-{{ $cap->id }}" style="display:none;margin-top:15px;border-top:2px solid #007bff;padding-top:15px;">
                <h5 style="color:#007bff;margin-bottom:10px;">
                  <i class="fas fa-tasks"></i> Progreso de Etapas
                </h5>

                @php
                  // Obtener etapas del curso con su progreso
                  $etapas = DB::table('etapa')
                    ->join('curso', 'curso.id', '=', 'etapa.id_curso')
                    ->leftJoin('progreso_voluntario', function($join) use ($voluntario, $cap) {
                        $join->on('progreso_voluntario.id_etapa', '=', 'etapa.id')
                             ->where('progreso_voluntario.id_usuario', '=', $voluntario->id_usuario);
                    })
                    ->where('curso.id_capacitacion', $cap->id)
                    ->select(
                        'etapa.id',
                        'etapa.nombre',
                        'etapa.orden',
                        'progreso_voluntario.estado',
                        'progreso_voluntario.fecha_inicio',
                        'progreso_voluntario.fecha_finalizacion'
                    )
                    ->orderBy('etapa.orden')
                    ->get();
                @endphp

                @foreach($etapas as $etapa)
                  <div class="etapa-item" style="background:#f8f9fa;padding:10px;border-radius:6px;margin-bottom:10px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                      <span>
                        <strong>{{ $etapa->orden }}.</strong> {{ $etapa->nombre }}
                      </span>
                      <span class="badge badge-{{ 
                        $etapa->estado == 'completado' ? 'success' : 
                        ($etapa->estado == 'en_progreso' ? 'warning' : 'secondary') 
                      }}">
                        {{ $etapa->estado ?? 'No iniciado' }}
                      </span>
                    </div>

                    @if($etapa->fecha_inicio)
                      <small style="color:#666;">
                        Inicio: {{ \Carbon\Carbon::parse($etapa->fecha_inicio)->format('d/m/Y') }}
                        @if($etapa->fecha_finalizacion)
                          | Fin: {{ \Carbon\Carbon::parse($etapa->fecha_finalizacion)->format('d/m/Y') }}
                        @endif
                      </small>
                    @endif
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <p class="mensaje-vacio">No hay capacitaciones asignadas.</p>
    @endif
    </div>
  `;
  break;


      case 'encuestas':
        contenido.innerHTML = `
          <h2 class="titulo-seccion">Encuestas Realizadas</h2>
          @if(count($evaluaciones) > 0)
            <div class="row">
              {{-- Columna Evaluación Física --}}
              <div class="col-md-6">
                @foreach($evaluaciones as $evaluacion)
                  <a href="{{ route('reporte.ver', ['id' => $evaluacion->reporte_id ?? $evaluacion->id_reporte ?? $evaluacion->id, 'tipo' => 'fisico']) }}" style="text-decoration: none;">
                    <div class="card mb-3" style="border-left: 4px solid #353b41; background-color: #f4f6f9; cursor: pointer; transition: all 0.2s;">
                      <div class="card-body d-flex justify-content-between align-items-center py-3">
                        <div>
                          <strong style="color: #353b41;">Evaluacion Fisica</strong>
                          <p class="mb-0 text-muted" style="font-size: 0.9rem;">Fecha realizada: {{ \Carbon\Carbon::parse($evaluacion->fecha_generado ?? $evaluacion->fecha)->format('j/n/Y') }}</p>
                        </div>
                        <span class="badge" style="background-color: #007bff; color: white; padding: 8px 12px; border-radius: 20px;">
                          # Reporte #{{ $evaluacion->reporte_id ?? $evaluacion->id_reporte ?? 'N/A' }}
                        </span>
                      </div>
                    </div>
                  </a>
                @endforeach
              </div>
              {{-- Columna Evaluación Emocional --}}
              <div class="col-md-6">
                @foreach($evaluaciones as $evaluacion)
                  <a href="{{ route('reporte.ver', ['id' => $evaluacion->reporte_id ?? $evaluacion->id_reporte ?? $evaluacion->id, 'tipo' => 'emocional']) }}" style="text-decoration: none;">
                    <div class="card mb-3" style="border-left: 4px solid #353b41; background-color: #f4f6f9; cursor: pointer; transition: all 0.2s;">
                      <div class="card-body d-flex justify-content-between align-items-center py-3">
                        <div>
                          <strong style="color: #353b41;">Evaluacion Emocional</strong>
                          <p class="mb-0 text-muted" style="font-size: 0.9rem;">Fecha realizada: {{ \Carbon\Carbon::parse($evaluacion->fecha_generado ?? $evaluacion->fecha)->format('j/n/Y') }}</p>
                        </div>
                        <span class="badge" style="background-color: #007bff; color: white; padding: 8px 12px; border-radius: 20px;">
                          # Reporte #{{ $evaluacion->reporte_id ?? $evaluacion->id_reporte ?? 'N/A' }}
                        </span>
                      </div>
                    </div>
                  </a>
                @endforeach
              </div>
            </div>
          @else
            <p class="mensaje-vacio">No hay encuestas realizadas.</p>
          @endif
        `;
        break;

      case 'cursos':
        contenido.innerHTML = `
          <h2 class="titulo-seccion">Cursos del Voluntario</h2>
          @if(count($cursos) > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
              @foreach($cursos as $curso)
                <div class="vista-card" 
                    style="cursor: pointer; transition: all 0.3s ease;" 
                    onclick="verDetalleCurso({{ $curso->id }}, {{ $voluntario->id_usuario }}, '{{ $curso->nombre }}', '{{ $curso->capacitacion_nombre }}', '{{ $curso->descripcion }}')">
                  
                  <strong>{{ $curso->nombre }}</strong>
                  <p>{{ $curso->descripcion }}</p>
                  <p><em>Capacitación: {{ $curso->capacitacion_nombre }}</em></p>
                  <div style="margin-top: 10px; color: #007bff; font-weight: bold;">
                    <i class="fas fa-eye"></i> Ver detalles y progreso
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <p class="mensaje-vacio">No hay cursos asignados.</p>
          @endif
        `;
        break;

      case 'necesidades':
        contenido.innerHTML = `
          <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;margin-bottom:20px;">
            <h2 class="titulo-seccion" style="margin-bottom:0;">Análisis de Necesidades</h2>
            <button class="btn-formulario-enviar" data-toggle="modal" data-target="#modalAsignarNecesidad">
              <i class="fas fa-plus-circle"></i> Asignar Necesidad
            </button>
          </div>

          @if(count($necesidadesAsignadas) > 0)
            @foreach($necesidadesAsignadas as $nec)
              <div class="vista-card">
                <div style="display:flex;justify-content:space-between;align-items:start;">
                  <div>
                    <strong>{{ $nec->tipo }}</strong>
                    <p>{{ $nec->descripcion }}</p>
                  </div>
                  <span class="badge badge-info" style="white-space:nowrap;">
                    {{ \Carbon\Carbon::parse($nec->fecha_generado)->format('d/m/Y') }}
                  </span>
                </div>
              </div>
            @endforeach
          @else
            <p class="mensaje-vacio">No hay necesidades asignadas.</p>
          @endif
        `;
        break;
    }
  }


  function toggleCursoDetalles(cursoId) {
  const detalles = document.getElementById('detalles-curso-' + cursoId);
  const icono = document.getElementById('icono-curso-' + cursoId);
  
  if (detalles.style.display === 'none') {
    detalles.style.display = 'block';
    icono.classList.remove('fa-chevron-down');
    icono.classList.add('fa-chevron-up');
  } else {
    detalles.style.display = 'none';
    icono.classList.remove('fa-chevron-up');
    icono.classList.add('fa-chevron-down');
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

  // Funciones para Toast Notifications
  function showToast(toastId) {
    const toast = document.getElementById(toastId);
    toast.style.display = 'block';
  }

  function hideToast(toastId) {
    const toast = document.getElementById(toastId);
    toast.style.animation = 'slideOut 0.3s ease';
    setTimeout(() => {
      toast.style.display = 'none';
      toast.style.animation = 'slideIn 0.3s ease';
    }, 300);
  }

  function hideAllToasts() {
    ['toast-loading', 'toast-success', 'toast-error'].forEach(id => {
      document.getElementById(id).style.display = 'none';
    });
  }

  // Función para enviar formulario al voluntario
  function enviarFormularioVoluntario(voluntarioId) {
    // Deshabilitar botón para evitar múltiples envíos
    const btn = document.getElementById('btn-enviar-formulario');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

    // Mostrar toast de cargando
    hideAllToasts();
    showToast('toast-loading');

    // Obtener CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Hacer la petición
    fetch(`/voluntarios/${voluntarioId}/enviar-formulario`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      },
      body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
      hideAllToasts();
      
      if (data.success) {
        document.getElementById('toast-success-msg').textContent = data.message;
        showToast('toast-success');
        
        // Auto-hide después de 5 segundos
        setTimeout(() => {
          hideToast('toast-success');
        }, 5000);
      } else {
        document.getElementById('toast-error-msg').textContent = data.message;
        showToast('toast-error');
      }
    })
    .catch(error => {
      hideAllToasts();
      console.error('Error:', error);
      document.getElementById('toast-error-msg').textContent = 'Error de conexión. Intente nuevamente.';
      showToast('toast-error');
    })
    .finally(() => {
      // Rehabilitar botón
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-paper-plane"></i> Enviar Formulario';
    });
  }


  /**
 * ✅ Mostrar detalles y progreso de un curso específico
 */
function verDetalleCurso(cursoId, voluntarioId, nombreCurso, nombreCapacitacion, descripcionCurso) {
  const contenido = document.getElementById('vista-contenido');
  
  // Mostrar loading
  contenido.innerHTML = `
    <div style="text-align: center; padding: 40px;">
      <div class="spinner-border text-primary" role="status">
        <span class="sr-only">Cargando...</span>
      </div>
      <p style="margin-top: 15px; color: #666;">Cargando detalles del curso...</p>
    </div>
  `;

  // Obtener las etapas del curso con su progreso
  fetch(`/api/cursos/${cursoId}/progreso/${voluntarioId}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        const etapas = data.etapas;

        let etapasHTML = '';
        etapas.forEach((etapa, index) => {
          const estadoColor = etapa.estado === 'completado' ? '#28a745' : 
                              etapa.estado === 'en_progreso' ? '#007bff' : '#6c757d';
          const estadoTexto = etapa.estado === 'completado' ? 'COMPLETADO' : 
                              etapa.estado === 'en_progreso' ? 'EN PROGRESO' : 'NO INICIADO';
          const estadoIcono = etapa.estado === 'completado' ? 'check-circle' : 
                              etapa.estado === 'en_progreso' ? 'clock' : 'circle';

          etapasHTML += `
            <div class="vista-card" style="border-left: 4px solid ${estadoColor};">
              <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                  <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                    <div style="
                      width: 32px;
                      height: 32px;
                      border-radius: 50%;
                      background: ${estadoColor};
                      color: white;
                      display: flex;
                      align-items: center;
                      justify-content: center;
                      font-weight: bold;
                    ">${index + 1}</div>
                    <strong style="font-size: 1.1rem;">${etapa.nombre}</strong>
                  </div>
                  <p style="color: #666; margin-left: 42px;">${etapa.descripcion || 'Sin descripción'}</p>
                </div>
                <div style="text-align: right;">
                  <span style="
                    background: ${estadoColor};
                    color: white;
                    padding: 6px 12px;
                    border-radius: 20px;
                    font-size: 0.85rem;
                    font-weight: 600;
                    display: inline-flex;
                    align-items: center;
                    gap: 5px;
                  ">
                    <i class="fas fa-${estadoIcono}"></i>
                    ${estadoTexto}
                  </span>
                </div>
              </div>
              ${etapa.fecha_inicio ? `
                <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #eee; font-size: 0.9rem; color: #666;">
                  <i class="fas fa-calendar-alt"></i> Inicio: ${new Date(etapa.fecha_inicio).toLocaleDateString('es-ES')}
                  ${etapa.fecha_finalizacion ? `
                    <br><i class="fas fa-calendar-check"></i> Finalizado: ${new Date(etapa.fecha_finalizacion).toLocaleDateString('es-ES')}
                  ` : ''}
                </div>
              ` : ''}
            </div>
          `;
        });

        contenido.innerHTML = `
          <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px;">
            <button class="btn-formulario-enviar" onclick="mostrarVista('cursos')" style="padding: 8px 16px;">
              <i class="fas fa-arrow-left"></i> Volver
            </button>
            <div>
              <h2 class="titulo-seccion" style="margin: 0;">${nombreCurso}</h2>
              <p style="color: #666; margin: 5px 0 0 0;">
                <i class="fas fa-certificate"></i> ${nombreCapacitacion}
              </p>
            </div>
          </div>

          <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <p style="margin: 0; color: #333;"><strong>Descripción:</strong> ${descripcionCurso || 'Sin descripción'}</p>
          </div>

          <h3 style="color: #007bff; margin-bottom: 15px;">
            <i class="fas fa-list-ol"></i> Etapas del Curso
          </h3>

          ${etapasHTML}
        `;
      } else {
        contenido.innerHTML = `
          <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> ${data.message}
          </div>
        `;
      }
    })
    .catch(error => {
      console.error('Error:', error);
      contenido.innerHTML = `
        <div class="alert alert-danger">
          <i class="fas fa-exclamation-triangle"></i> Error al cargar el curso
        </div>
      `;
    });
}


</script>
@endsection