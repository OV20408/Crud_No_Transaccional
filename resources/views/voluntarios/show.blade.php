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

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: scale(0.95);
      }
      to {
        opacity: 1;
        transform: scale(1);
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

    @keyframes pulseNuevo {
    0%, 100% {
      opacity: 1;
    }
    50% {
      opacity: 0.6;
    }
  }

  /* Estilos de Paginación */
    .pagination-container {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 10px;
      margin-top: 30px;
      padding: 20px 0;
    }


  /* Paginación del historial centrada debajo de ambas columnas */
  .historial-tabla + .pagination-container {
    width: 100%;
    margin-top: 30px;
    padding: 20px 0;
    border-top: 2px solid #e0e0e0;
  }

    .pagination-btn {
      padding: 10px 16px;
      background: var(--color-card);
      border: 2px solid var(--color-azul);
      color: var(--color-azul);
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 14px;
      min-width: 45px;
      text-align: center;
    }

    .pagination-btn:hover:not(:disabled) {
      background: var(--color-azul);
      color: white;
      transform: translateY(-2px);
    }

    .pagination-btn.active {
      background: var(--color-azul);
      color: white;
      box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    }

    .pagination-btn:disabled {
      opacity: 0.3;
      cursor: not-allowed;
      border-color: #ccc;
      color: #ccc;
    }

    .pagination-arrow {
      padding: 10px 14px;
      background: var(--color-card);
      border: 2px solid var(--color-azul);
      color: var(--color-azul);
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 16px;
    }

    .pagination-arrow:hover:not(:disabled) {
      background: var(--color-azul);
      color: white;
      transform: scale(1.1);
    }

    .pagination-arrow:disabled {
      opacity: 0.3;
      cursor: not-allowed;
      border-color: #ccc;
      color: #ccc;
    }

    .pagination-info {
      font-size: 14px;
      color: #666;
      font-weight: 500;
      padding: 0 15px;
    }

    .pagination-numbers {
      display: flex;
      gap: 8px;
    }

    @media (max-width: 768px) {
      .pagination-container {
        flex-wrap: wrap;
        gap: 8px;
      }
      
      .pagination-btn {
        min-width: 40px;
        padding: 8px 12px;
        font-size: 13px;
      }
      
      .pagination-arrow {
        padding: 8px 12px;
      }
      
      .pagination-info {
        width: 100%;
        text-align: center;
        padding: 10px 0;
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


    <div class="modal fade" id="modalConfirmarCertificado" tabindex="-1" role="dialog" aria-labelledby="modalConfirmarCertificadoLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 8px 30px rgba(0,0,0,0.15);">
          <div class="modal-header" style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: white; border-radius: 12px 12px 0 0;">
            <h5 class="modal-title" id="modalConfirmarCertificadoLabel" style="font-weight: bold;">
              <i class="fas fa-certificate"></i> Generar Certificado
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.8;">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="modal-body" style="padding: 30px;">
            <div style="text-align: center; margin-bottom: 20px;">
              <i class="fas fa-award" style="font-size: 4rem; color: #007bff;"></i>
            </div>
            <p style="text-align: center; font-size: 1.1rem; color: #333; margin-bottom: 10px;">
              ¿Estás seguro de generar el certificado para:
            </p>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid #007bff; margin-bottom: 20px;">
              <strong style="color: #007bff; font-size: 1.1rem;" id="modal-capacitacion-nombre"></strong>
            </div>
            <p style="font-size: 0.9rem; color: #666; text-align: center;">
              <i class="fas fa-info-circle"></i> El certificado se generará en PDF y se enviará automáticamente al email del voluntario.
            </p>
          </div>

          <div class="modal-footer" style="border-top: 1px solid #dee2e6; padding: 15px 30px;">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 10px 20px;">
              <i class="fas fa-times"></i> Cancelar
            </button>
            <button type="button" class="btn btn-primary" onclick="confirmarGenerarCertificado()" style="background: #007bff; border: none; border-radius: 8px; padding: 10px 20px; font-weight: 600;">
              <i class="fas fa-check"></i> Generar Certificado
            </button>
          </div>
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

          <a href="{{ route('voluntarios.capacitaciones.pdf', $voluntario->id_usuario) }}?v={{ time() }}" 
            class="btn-formulario-enviar" 
            style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
              <i class="fas fa-file-pdf"></i> Descargar Capacitaciones
          </a>

          <a href="{{ route('voluntarios.necesidades.pdf', $voluntario->id_usuario) }}?v={{ time() }}"
            class="btn-formulario-enviar" 
            style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
              <i class="fas fa-file-pdf"></i> Descargar Necesidades
          </a>

          {{-- ✅ NUEVO BOTÓN PARA CAMBIAR ESTADO --}}
          @if(strtolower($voluntario->estado) === 'activo')
            <button class="btn-formulario-enviar" 
                    onclick="cambiarEstadoVoluntario({{ $voluntario->id_usuario }}, 'inactivo')" 
                    style="background-color: #dc3545;" 
                    id="btn-cambiar-estado">
              <i class="fas fa-user-slash"></i> Marcar como Inactivo
            </button>
          @else
            <button class="btn-formulario-enviar" 
                    onclick="cambiarEstadoVoluntario({{ $voluntario->id_usuario }}, 'activo')" 
                    style="background-color: #28a745;" 
                    id="btn-cambiar-estado">
              <i class="fas fa-user-check"></i> Reactivar Voluntario
            </button>
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
        <p><i class="fas fa-calendar-alt"></i> {{ $voluntario->fecha_nacimiento ? \Carbon\Carbon::parse($voluntario->fecha_nacimiento)->format('d/m/Y') : 'N/D' }}</p>
        <p><i class="fas fa-venus-mars"></i> {{ $voluntario->genero ?? 'N/D' }}</p>
        <p><i class="fas fa-phone"></i> {{ $voluntario->telefono ?? 'N/D' }}</p>
        <p><i class="fas fa-tint"></i> {{ $voluntario->tipo_sangre ?? 'N/D' }}</p>
        <p><i class="fas fa-map-marker-alt"></i> {{ $voluntario->direccion_domicilio ?? 'N/D' }}</p>
        <p><i class="fas fa-id-card"></i> {{ $voluntario->ci }}</p>
      </div>

      {{-- Evaluaciones Físicas --}}
      <div class="panel-hover panel-fisico" id="panel-evaluacion-fisica">
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
      <div class="panel-hover panel-psicologico" id="panel-evaluacion-psicologica">
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

  {{-- Modal para asignar curso --}}
  <div class="modal fade" id="modalAsignarCurso" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <form method="POST" action="{{ route('voluntarios.cursos.asignar', $voluntario->id_usuario) }}" class="modal-content">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Asignar Curso al Voluntario</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <label for="curso_id">Selecciona un Curso</label>
            <select name="curso_id" id="curso_id" class="form-control" required onchange="mostrarInfoCurso(this.value)">
              <option value="">-- Selecciona un curso --</option>
              @php
                $cursosDisponibles = DB::table('curso')
                  ->join('capacitacion', 'curso.id_capacitacion', '=', 'capacitacion.id')
                  ->select('curso.id', 'curso.nombre', 'curso.descripcion', 'capacitacion.nombre as capacitacion_nombre')
                  ->orderBy('capacitacion.nombre')
                  ->orderBy('curso.nombre')
                  ->get();
              @endphp
              @foreach($cursosDisponibles as $curso)
                <option value="{{ $curso->id }}" 
                        data-descripcion="{{ $curso->descripcion }}"
                        data-capacitacion="{{ $curso->capacitacion_nombre }}">
                  {{ $curso->nombre }} ({{ $curso->capacitacion_nombre }})
                </option>
              @endforeach
            </select>
          </div>

          <div id="info-curso" style="display: none; background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 15px;">
            <p style="margin: 0 0 5px 0;"><strong>Descripción:</strong></p>
            <p id="curso-descripcion" style="margin: 0; color: #666; font-size: 14px;"></p>
          </div>

          @if($errors->any())
            <div class="alert alert-danger mt-2">
              {{ $errors->first() }}
            </div>
          @endif
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Asignar Curso</button>
        </div>
      </form>
    </div>
  </div>

  <script>

  /**
   * Cambiar estado del voluntario (activo/inactivo)
   */
  function cambiarEstadoVoluntario(idUsuario, nuevoEstado) {
    const mensajes = {
      'inactivo': '¿Desactivar este voluntario?',
      'activo': '¿Reactivar este voluntario?'
    };
    
    if (!confirm(mensajes[nuevoEstado])) {
      return;
    }
    
    const btn = document.getElementById('btn-cambiar-estado');
    const textoOriginal = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
    
    hideAllToasts();
    showToast('toast-loading');

    fetch(`/voluntarios/${idUsuario}/cambiar-estado`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ estado: nuevoEstado })
    })
    .then(response => response.json())
    .then(data => {
      hideAllToasts();
      
      if (data.success) {
        document.getElementById('toast-success-msg').textContent = data.message;
        showToast('toast-success');
        
        setTimeout(() => {
          if (nuevoEstado === 'inactivo') {
            window.location.href = '/voluntarios/inactivos';
          } else {
            window.location.reload();
          }
        }, 1500);
      } else {
        document.getElementById('toast-error-msg').textContent = data.message;
        showToast('toast-error');
        btn.disabled = false;
        btn.innerHTML = textoOriginal;
      }
    })
    .catch(error => {
      console.error('Error:', error);
      hideAllToasts();
      document.getElementById('toast-error-msg').textContent = 'Error al cambiar el estado';
      showToast('toast-error');
      btn.disabled = false;
      btn.innerHTML = textoOriginal;
    });
  }

  function mostrarInfoCurso(cursoId) {
    const select = document.getElementById('curso_id');
    const selectedOption = select.options[select.selectedIndex];
    const infoCurso = document.getElementById('info-curso');
    const descripcion = document.getElementById('curso-descripcion');
    
    if (cursoId && selectedOption) {
      const desc = selectedOption.getAttribute('data-descripcion');
      descripcion.textContent = desc || 'Sin descripción';
      infoCurso.style.display = 'block';
    } else {
      infoCurso.style.display = 'none';
    }
  }
  </script>

  </div>

  <script>
    // ========== INICIO: CÓDIGO DE PAGINACIÓN ==========
    let paginaActualEncuestas = 1;
    let paginaActualReportes = 1;
    let paginaActualHistorial = 1;
    const itemsPorPagina = 10;

    // Función para crear controles de paginación
    function crearPaginacion(totalItems, paginaActual, onPageChange) {
      const totalPaginas = Math.ceil(totalItems / itemsPorPagina);
      
      if (totalPaginas <= 1) return '';
      
      let html = '<div class="pagination-container">';
      
      // Botón anterior
      html += `
        <button class="pagination-arrow" 
                onclick="${onPageChange}(${paginaActual - 1})" 
                ${paginaActual === 1 ? 'disabled' : ''}>
          <i class="fas fa-chevron-left"></i>
        </button>
      `;
      
      // Números de página
      html += '<div class="pagination-numbers">';
      
      // Lógica para mostrar números de página
      const maxBotones = 5;
      let inicio = Math.max(1, paginaActual - Math.floor(maxBotones / 2));
      let fin = Math.min(totalPaginas, inicio + maxBotones - 1);
      
      if (fin - inicio < maxBotones - 1) {
        inicio = Math.max(1, fin - maxBotones + 1);
      }
      
      // Primera página si no está en el rango
      if (inicio > 1) {
        html += `<button class="pagination-btn" onclick="${onPageChange}(1)">1</button>`;
        if (inicio > 2) {
          html += '<span class="pagination-info">...</span>';
        }
      }
      
      // Páginas numeradas
      for (let i = inicio; i <= fin; i++) {
        html += `
          <button class="pagination-btn ${i === paginaActual ? 'active' : ''}" 
                  onclick="${onPageChange}(${i})">
            ${i}
          </button>
        `;
      }
      
      // Última página si no está en el rango
      if (fin < totalPaginas) {
        if (fin < totalPaginas - 1) {
          html += '<span class="pagination-info">...</span>';
        }
        html += `<button class="pagination-btn" onclick="${onPageChange}(${totalPaginas})">${totalPaginas}</button>`;
      }
      
      html += '</div>';
      
      // Botón siguiente
      html += `
        <button class="pagination-arrow" 
                onclick="${onPageChange}(${paginaActual + 1})" 
                ${paginaActual === totalPaginas ? 'disabled' : ''}>
          <i class="fas fa-chevron-right"></i>
        </button>
      `;
      
      // Información de página actual
      html += `
        <div class="pagination-info">
          Página ${paginaActual} de ${totalPaginas} (${totalItems} items)
        </div>
      `;
      
      html += '</div>';
      
      return html;
    }

    // Función para cambiar página de encuestas
    function cambiarPaginaEncuestas(nuevaPagina) {
      paginaActualEncuestas = nuevaPagina;
      renderizarEncuestas();
      document.getElementById('vista-contenido').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Función para cambiar página de reportes
    function cambiarPaginaReportes(nuevaPagina) {
      paginaActualReportes = nuevaPagina;
      renderizarReportes();
      document.getElementById('vista-contenido').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Función para cambiar página de historial
  function cambiarPaginaHistorial(nuevaPagina) {
    paginaActualHistorial = nuevaPagina;
    renderizarHistorial();
    document.getElementById('vista-contenido').scrollIntoView({ behavior: 'smooth', block: 'start' });
  }


    let evaluacionesActuales = @json($evaluaciones ?? []);
    let reportesActuales = @json($reportes ?? []);
    let reportesNoVistos = @json($reportesNoVistos ?? []);
    

    function renderizarEncuestas() {
      const contenido = document.getElementById('vista-contenido');
      if (!contenido) return;
      
      let html = '<h2 class="titulo-seccion">Encuestas Realizadas</h2>';
      
      if (evaluacionesActuales && evaluacionesActuales.length > 0) {
        // Calcular índices para la paginación
        const inicio = (paginaActualEncuestas - 1) * itemsPorPagina;
        const fin = inicio + itemsPorPagina;
        const evaluacionesPaginadas = evaluacionesActuales.slice(inicio, fin);
        
        html += '<div class="row">';
        
        // Columna Evaluación Física
        html += '<div class="col-md-6">';
        evaluacionesPaginadas.forEach(function(eval) {
          const reporteId = eval.reporte_id || eval.id_reporte || eval.id || 'N/A';
          const fechaRaw = eval.fecha_generado || eval.fecha;
          const fecha = fechaRaw ? new Date(fechaRaw).toLocaleDateString('es-ES') : 'N/A';
          
          const reporteNoVisto = reportesNoVistos.find(r => r.reporte_id == reporteId);
          const esNuevoFisico = reporteNoVisto && reporteNoVisto.fisico_no_visto === 'fisico';
          
          html += `
            <a href="/reporte/${reporteId}/fisico" style="text-decoration: none; position: relative;">
              <div class="card mb-3" style="border-left: 4px solid #353b41; background-color: #f4f6f9; cursor: pointer; transition: all 0.2s;">
                ${esNuevoFisico ? `
                  <div style="position: absolute; top: 10px; right: 10px; display: flex; flex-direction: column; align-items: center; gap: 5px; animation: pulseNuevo 2s infinite;">
                    <div style="width: 12px; height: 12px; background-color: #007bff; border-radius: 50%; box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.3);"></div>
                    <span style="background-color: #007bff; color: white; font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Nueva</span>
                  </div>
                ` : ''}
                <div class="card-body d-flex justify-content-between align-items-center py-3">
                  <div>
                    <strong style="color: #353b41;">Evaluacion Fisica</strong>
                    <p class="mb-0 text-muted" style="font-size: 0.9rem;">Fecha realizada: ${fecha}</p>
                  </div>
                  <span class="badge" style="background-color: #007bff; color: white; padding: 8px 12px; border-radius: 20px;">
                    # Reporte #${reporteId}
                  </span>
                </div>
              </div>
            </a>
          `;
        });
        html += '</div>';
        
        // Columna Evaluación Emocional
        html += '<div class="col-md-6">';
        evaluacionesPaginadas.forEach(function(eval) {
          const reporteId = eval.reporte_id || eval.id_reporte || eval.id || 'N/A';
          const fechaRaw = eval.fecha_generado || eval.fecha;
          const fecha = fechaRaw ? new Date(fechaRaw).toLocaleDateString('es-ES') : 'N/A';
          
          const reporteNoVisto = reportesNoVistos.find(r => r.reporte_id == reporteId);
          const esNuevoEmocional = reporteNoVisto && reporteNoVisto.emocional_no_visto === 'emocional';
          
          html += `
            <a href="/reporte/${reporteId}/emocional" style="text-decoration: none; position: relative;">
              <div class="card mb-3" style="border-left: 4px solid #353b41; background-color: #f4f6f9; cursor: pointer; transition: all 0.2s;">
                ${esNuevoEmocional ? `
                  <div style="position: absolute; top: 10px; right: 10px; display: flex; flex-direction: column; align-items: center; gap: 5px; animation: pulseNuevo 2s infinite;">
                    <div style="width: 12px; height: 12px; background-color: #007bff; border-radius: 50%; box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.3);"></div>
                    <span style="background-color: #007bff; color: white; font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Nueva</span>
                  </div>
                ` : ''}
                <div class="card-body d-flex justify-content-between align-items-center py-3">
                  <div>
                    <strong style="color: #353b41;">Evaluacion Emocional</strong>
                    <p class="mb-0 text-muted" style="font-size: 0.9rem;">Fecha realizada: ${fecha}</p>
                  </div>
                  <span class="badge" style="background-color: #007bff; color: white; padding: 8px 12px; border-radius: 20px;">
                    # Reporte #${reporteId}
                  </span>
                </div>
              </div>
            </a>
          `;
        });
        html += '</div>';
        
        html += '</div>';
        
        // Agregar controles de paginación
        html += crearPaginacion(evaluacionesActuales.length, paginaActualEncuestas, 'cambiarPaginaEncuestas');
        
      } else {
        html += '<p class="mensaje-vacio">No hay encuestas realizadas.</p>';
      }
      
      contenido.innerHTML = html;
    }




    // Función para renderizar historial con datos dinámicos
    // Función para renderizar historial con datos dinámicos
    // Función para renderizar historial con datos dinámicos Y PAGINACIÓN
  // Función para renderizar historial con datos dinámicos Y PAGINACIÓN UNIFICADA
  function renderizarHistorial() {
    const contenido = document.getElementById('vista-contenido');
    if (!contenido) return;
    
    let html = '<h2 class="titulo-seccion">Historial</h2>';
    
    if (reportesActuales && reportesActuales.length > 0) {
      // Calcular paginación ANTES de separar por columnas
      const totalReportes = reportesActuales.length;
      const inicio = (paginaActualHistorial - 1) * itemsPorPagina;
      const fin = inicio + itemsPorPagina;
      const reportesPaginados = reportesActuales.slice(inicio, fin);
      
      html += '<div class="historial-tabla">';
      
      // ========== COLUMNA CLÍNICO ==========
      html += '<div class="historial-columna">';
      html += '<div class="historial-columna-header"><i class="fas fa-heartbeat mr-2"></i> Clínico</div>';
      
      // Renderizar solo los reportes de la página actual
      let hayClinico = false;
      reportesPaginados.forEach(function(reporte) {
        if (reporte.resumen_fisico) {
          hayClinico = true;
          const fecha = new Date(reporte.fecha_generado).toLocaleDateString('es-ES');
          html += `
            <div class="historial-item">
              <div class="historial-item-content">${reporte.resumen_fisico}</div>
              <div class="historial-item-fecha">${fecha}</div>
            </div>
          `;
        }
      });
      
      if (!hayClinico) {
        html += '<p class="mensaje-vacio" style="padding: 20px; text-align: center; color: #999;">No hay registros clínicos en esta página.</p>';
      }
      
      html += '</div>';
      
      // ========== COLUMNA PSICOLÓGICO ==========
      html += '<div class="historial-columna">';
      html += '<div class="historial-columna-header psicologico"><i class="fas fa-brain mr-2"></i> Psicológico</div>';
      
      // Renderizar solo los reportes de la página actual
      let hayPsicologico = false;
      reportesPaginados.forEach(function(reporte) {
        if (reporte.resumen_emocional) {
          hayPsicologico = true;
          const fecha = new Date(reporte.fecha_generado).toLocaleDateString('es-ES');
          html += `
            <div class="historial-item psicologico">
              <div class="historial-item-content">${reporte.resumen_emocional}</div>
              <div class="historial-item-fecha">${fecha}</div>
            </div>
          `;
        }
      });
      
      if (!hayPsicologico) {
        html += '<p class="mensaje-vacio" style="padding: 20px; text-align: center; color: #999;">No hay registros psicológicos en esta página.</p>';
      }
      
      html += '</div>';
      
      html += '</div>';
      
      // ========== PAGINACIÓN UNIFICADA (fuera de las columnas) ==========
      html += crearPaginacion(totalReportes, paginaActualHistorial, 'cambiarPaginaHistorial');
      
    } else {
      html += '<p class="mensaje-vacio">No hay historial disponible.</p>';
    }
    
    contenido.innerHTML = html;
  }


    // Función para renderizar reportes con datos dinámicos
    // Función mejorada para renderizar reportes CON PAGINACIÓN
    function renderizarReportes() {
      const contenido = document.getElementById('vista-contenido');
      if (!contenido) return;
      
      let html = '<h2 class="titulo-seccion">Reportes</h2>';
      
      if (reportesActuales && reportesActuales.length > 0) {
        // Calcular índices para la paginación
        const inicio = (paginaActualReportes - 1) * itemsPorPagina;
        const fin = inicio + itemsPorPagina;
        const reportesPaginados = reportesActuales.slice(inicio, fin);
        
        reportesPaginados.forEach(function(reporte) {
          const fecha = new Date(reporte.fecha_generado).toLocaleString('es-ES');
          html += `
            <div class="vista-card">
              <strong>Reporte #${reporte.id}</strong>
              <p><strong>Estado:</strong></p>
              <p>${reporte.estado_general || 'Procesado por IA'}</p>
              <p><strong>Fecha:</strong></p>
              <p>${fecha}</p>
              ${reporte.observaciones ? `<p><strong>Observaciones:</strong></p><p>${reporte.observaciones}</p>` : ''}
            </div>
          `;
        });
        
        // Agregar controles de paginación
        html += crearPaginacion(reportesActuales.length, paginaActualReportes, 'cambiarPaginaReportes');
        
      } else {
        html += '<p class="mensaje-vacio">No hay reportes disponibles.</p>';
      }
      
      contenido.innerHTML = html;
    }

    function mostrarVista(vista) {
      // Resetear paginación cuando se cambia de vista
      if (vista === 'encuestas') {
        paginaActualEncuestas = 1;
      } else if (vista === 'reportes') {
        paginaActualReportes = 1;
      }
      const contenido = document.getElementById('vista-contenido');
      
      switch(vista) {
        case 'historial':
          // Usar función dinámica para historial
          renderizarHistorial();
          break;

        case 'reportes':
          // Usar función dinámica para reportes
          renderizarReportes();
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
            @php
              $etapas = DB::table('progreso_voluntario')
                  ->join('etapa', 'etapa.id', '=', 'progreso_voluntario.id_etapa')
                  ->join('curso', 'curso.id', '=', 'etapa.id_curso')
                  ->where('curso.id_capacitacion', $cap->id)
                  ->where('progreso_voluntario.id_usuario', $voluntario->id_usuario)
                  ->get();
              
              $todasCompletadas = $etapas->every(function($etapa) {
                  return $etapa->estado === 'completado';
              });

              // Verificar si ya tiene certificado
              $tieneCertificado = DB::table('certificados')
                  ->where('id_usuario', $voluntario->id_usuario)
                  ->where('id_capacitacion', $cap->id)
                  ->where('estado', 'activo')
                  ->exists();
            @endphp

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
                          {{ $etapa->estado == 'en_progreso' ? 'En progreso' : ($etapa->estado == 'completado' ? 'Completado' : ($etapa->estado ?? 'No iniciado')) }}
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

                  {{-- ✅ BOTÓN DE CERTIFICADO (si todas las etapas están completadas) --}}
                  @if($todasCompletadas)
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 2px solid #28a745;">
                      @if($tieneCertificado)
                        <button class="btn-formulario-enviar" style="background:#28a745;width:100%;" onclick="event.stopPropagation(); verCertificado({{ $voluntario->id_usuario }}, {{ $cap->id }})">
                          <i class="fas fa-certificate"></i> Ver Certificado
                        </button>
                      @else
                        <button class="btn-formulario-enviar" style="background:#007bff;color:#fff;width:100%;" onclick="event.stopPropagation(); generarCertificado({{ $voluntario->id_usuario }}, {{ $cap->id }})">
                          <i class="fas fa-award"></i> Generar Certificado
                        </button>
                      @endif
                    </div>
                  @endif
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
          // Usar la función dinámica para renderizar encuestas con datos actualizados
          renderizarEncuestas();
          break;

        case 'cursos':
          contenido.innerHTML = `
            {{-- HEADER CON TÍTULO, RECOMENDACIÓN Y BOTÓN EN UNA FILA --}}
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 20px; margin-bottom: 30px; flex-wrap: wrap;">
              
              {{-- TÍTULO A LA IZQUIERDA --}}
              <div style="flex: 0 0 auto;">
                <h2 class="titulo-seccion" style="margin: 0; white-space: nowrap;">Cursos del Voluntario</h2>
              </div>
              
              {{-- RECOMENDACIÓN DE LA IA EN EL CENTRO (DINÁMICO) --}}
              <div id="recomendacion-ia-container" style="flex: 1; display: flex; justify-content: center; margin: 0 20px;">
              </div>

              {{-- BOTÓN A LA DERECHA --}}
              <div style="flex: 0 0 auto;">
                <button class="btn-formulario-enviar" data-toggle="modal" data-target="#modalAsignarCurso" style="white-space: nowrap;">
                  <i class="fas fa-plus-circle"></i> Asignar Curso
                </button>
              </div>
            </div>

            {{-- CURSOS ASIGNADOS --}}
            @if(count($cursos) > 0)
              <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                @foreach($cursos as $curso)
                  <div class="vista-card" 
                      style="cursor: pointer; transition: all 0.3s ease;" 
                      onclick="verDetalleCurso({{ $curso->id }}, {{ $voluntario->id_usuario }}, '{{ addslashes($curso->nombre) }}', '{{ addslashes($curso->capacitacion_nombre) }}', '{{ addslashes($curso->descripcion ?? '') }}')">
                    
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
              <p class="mensaje-vacio">No hay cursos asignados aún. Usa el botón de arriba para asignar cursos.</p>
            @endif
          `;
          
          // Renderizar recomendaciones dinámicamente
          renderizarRecomendaciones();
          break;

        case 'necesidades':
          contenido.innerHTML = `
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;">
              <h2 class="titulo-seccion" style="margin-bottom:0;flex-shrink:0;">Análisis de Necesidades</h2>
              
              <!-- Aptitud del voluntario evaluada por IA -->
              <div id="aptitud-necesidades-container" style="flex:1;"></div>
              
              <button class="btn-formulario-enviar" data-toggle="modal" data-target="#modalAsignarNecesidad" style="flex-shrink:0;">
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
          // Renderizar aptitud después de crear el contenedor
          renderizarAptitudNecesidades();
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

  // ========================================
  // RENDERIZAR RECOMENDACIONES DE CURSOS (MÚLTIPLES)
  // ========================================
  function renderizarRecomendaciones() {
    const container = document.getElementById('recomendacion-ia-container');
    if (!container) return;
    
    if (recomendacionesActuales && recomendacionesActuales.length > 0) {
      // Crear un wrapper flex para las recomendaciones
      let html = '<div style="display: flex; gap: 10px; width: 100%;">';
      
      recomendacionesActuales.forEach((recom, index) => {
        const fechaCreacion = new Date(recom.created_at);
        const fechaActualizacion = new Date(recom.updated_at);
        const esActualizado = fechaActualizacion > fechaCreacion;
        
        const fechaMostrar = esActualizado 
          ? '<i class="fas fa-sync-alt"></i> Actualizado' 
          : '<i class="fas fa-clock"></i> ' + fechaCreacion.toLocaleDateString('es-ES');
        
        const badgeCapacitacion = recom.capacitacion_nombre 
          ? `<span style="background: #e3f2fd; color: #1976d2; padding: 2px 8px; border-radius: 10px; font-size: 10px;">
              ${recom.capacitacion_nombre}
            </span>` 
          : '';
        
        const razonTexto = recom.razon 
          ? `<span style="color: #666; font-weight: normal;"> - ${recom.razon}</span>` 
          : '';
        
        html += `
          <div style="
            background: var(--color-card); 
            border-left: 4px solid var(--color-azul);
            padding: 10px 20px; 
            border-radius: 8px; 
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            flex: 1;
            animation: fadeIn 0.3s ease;
          ">
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 15px;">
              <div style="display: flex; align-items: center; gap: 8px; color: var(--color-azul);">
                <i class="fas fa-robot" style="font-size: 14px;"></i>
                <strong style="font-size: 13px;">Recomendación ${recomendacionesActuales.length > 1 ? (index + 1) : 'de IA'}</strong>
              </div>
              
              <div style="display: flex; align-items: center; gap: 15px; font-size: 11px; color: #999;">
                <span>${fechaMostrar}</span>
                ${badgeCapacitacion}
              </div>
            </div>
            
            <div style="display: flex; align-items: center; gap: 10px; margin-top: 8px;">
              <i class="fas fa-lightbulb" style="color: #ffc107; font-size: 13px; flex-shrink: 0;"></i>
              <p style="margin: 0; font-size: 13px; color: var(--color-texto-principal); line-height: 1.3;">
                <strong>${recom.curso_nombre}</strong>
                ${razonTexto}
              </p>
            </div>
          </div>
        `;
      });
      
      html += '</div>';
      container.innerHTML = html;
    } else {
      container.innerHTML = '';
    }
  }

  // ========================================
  // RENDERIZAR APTITUD PARA NECESIDADES (IA)
  // ========================================
  function renderizarAptitudNecesidades() {
    const container = document.getElementById('aptitud-necesidades-container');
    if (!container) return;
    
    if (aptitudActual) {
      let colorBorde, colorFondo, colorTexto, icono, titulo;
      
      // Determinar estilo según nivel de aptitud
      switch(aptitudActual.nivel_aptitud) {
        case 'APTO_TODAS':
          colorBorde = '#4caf50';
          colorFondo = '#e8f5e9';
          colorTexto = '#2e7d32';
          icono = 'fa-check-circle';
          titulo = 'Apto para Todas';
          break;
        case 'APTO_ALGUNAS':
          colorBorde = '#ff9800';
          colorFondo = '#fff3e0';
          colorTexto = '#e65100';
          icono = 'fa-exclamation-triangle';
          titulo = 'Apto para Algunas';
          break;
        case 'NO_APTO':
          colorBorde = '#f44336';
          colorFondo = '#ffebee';
          colorTexto = '#c62828';
          icono = 'fa-times-circle';
          titulo = 'No Apto';
          break;
        default:
          colorBorde = '#9e9e9e';
          colorFondo = '#f5f5f5';
          colorTexto = '#616161';
          icono = 'fa-question-circle';
          titulo = 'Sin Evaluar';
      }
      
      // Mapeo de IDs a nombres de necesidades
      const necesidadesMap = {
        2: 'Asistencia Médica Básica',
        3: 'Apoyo Psicológico',
        4: 'Distribución de Alimentos',
        5: 'Logística y Coordinación',
        6: 'Rescate en Zonas de Riesgo',
        7: 'Atención a Niños',
        8: 'Transporte de Heridos',
        9: 'Comunicación y Registro'
      };
      
      // Construir lista de necesidades recomendadas
      let necesidadesHTML = '';
      if (aptitudActual.nivel_aptitud === 'APTO_TODAS') {
        necesidadesHTML = `<div style="margin-top: 6px; font-size: 10px; color: ${colorTexto};">✓ Puede realizar todas las necesidades disponibles</div>`;
      } else if (aptitudActual.nivel_aptitud === 'APTO_ALGUNAS' && aptitudActual.necesidades_recomendadas) {
        try {
          let necesidades = [];
          
          // Intentar parsear de diferentes formas
          if (Array.isArray(aptitudActual.necesidades_recomendadas)) {
            necesidades = aptitudActual.necesidades_recomendadas;
          } else if (typeof aptitudActual.necesidades_recomendadas === 'string') {
            necesidades = JSON.parse(aptitudActual.necesidades_recomendadas);
          }
          
          console.log('Necesidades parseadas:', necesidades);
          
          if (Array.isArray(necesidades) && necesidades.length > 0) {
            const listaNecesidades = necesidades.map(id => necesidadesMap[id] || `ID ${id}`).join(', ');
            necesidadesHTML = `<div style="margin-top: 6px; font-size: 10px; color: ${colorTexto};">✓ Puede realizar: ${listaNecesidades}</div>`;
          }
        } catch (e) {
          console.error('Error parseando necesidades_recomendadas:', e, aptitudActual.necesidades_recomendadas);
        }
      } else if (aptitudActual.nivel_aptitud === 'NO_APTO') {
        necesidadesHTML = `<div style="margin-top: 6px; font-size: 10px; color: ${colorTexto};">✗ No se recomienda asignar necesidades en este momento</div>`;
      }
      
      const html = `
        <div style="
          background: ${colorFondo}; 
          border-left: 4px solid ${colorBorde};
          padding: 6px 12px; 
          border-radius: 6px; 
          box-shadow: 0 1px 4px rgba(0,0,0,0.08);
          animation: fadeIn 0.3s ease;
        ">
          <div style="display: flex; align-items: center; gap: 8px;">
            <i class="fas ${icono}" style="font-size: 12px; color: ${colorTexto}; flex-shrink: 0;"></i>
            <strong style="font-size: 11px; color: ${colorTexto}; flex-shrink: 0;">${titulo}:</strong>
            <span style="font-size: 11px; color: ${colorTexto};">${aptitudActual.razon_ia || 'Sin evaluación'}</span>
          </div>
          ${necesidadesHTML}
        </div>
      `;
      
      container.innerHTML = html;
    } else {
      // Mostrar mensaje compacto cuando no hay datos
      container.innerHTML = `
        <div style="
          background: #f5f5f5; 
          border-left: 4px solid #9e9e9e;
          padding: 6px 12px; 
          border-radius: 6px; 
          box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        ">
          <div style="display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-info-circle" style="font-size: 12px; color: #616161; flex-shrink: 0;"></i>
            <span style="font-size: 11px; color: #757575;">Sin evaluación. Se generará tras la primera evaluación.</span>
          </div>
        </div>
      `;
    }
  }

  // ========================================
  // ACTUALIZACIÓN AUTOMÁTICA DE DATOS
  // ========================================
  let ultimoTotalReportes = {{ count($reportes ?? []) }};
  const voluntarioId = {{ $voluntario->id_usuario }};
  const INTERVALO_POLLING = 3000; // 3 segundos

  // Variable para detectar cambios
  let ultimaFechaEvaluacion = evaluacionesActuales.length > 0 ? (evaluacionesActuales[0].fecha_generado || evaluacionesActuales[0].fecha || '') : '';

  // Variable para recomendaciones de cursos (múltiples)
  let ultimoTotalRecomendaciones = {{ count($recomendacionesCursos ?? []) }};
  let ultimaRecomendacionFecha = '{{ count($recomendacionesCursos ?? []) > 0 ? $recomendacionesCursos[0]->updated_at : "" }}';
  let recomendacionesActuales = @json($recomendacionesCursos ?? []);

  // Variable para aptitud de necesidades
  let aptitudActual = @json($aptitudNecesidades ?? null);
  let ultimaAptitudFecha = '{{ $aptitudNecesidades->updated_at ?? "" }}';

  function actualizarDatosVoluntario() {
    fetch(`/voluntarios/${voluntarioId}/datos-actualizados`, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        const nuevosDatos = data.data;
        
        // Verificar si hay nuevos reportes O si cambió la última fecha de evaluación
        const nuevaFechaEvaluacion = nuevosDatos.evaluaciones && nuevosDatos.evaluaciones.length > 0 
          ? (nuevosDatos.evaluaciones[0].fecha_generado || nuevosDatos.evaluaciones[0].fecha || '') 
          : '';
        
        // Verificar cambios en recomendaciones de cursos
        const nuevoTotalRecomendaciones = nuevosDatos.recomendacionesCursos ? nuevosDatos.recomendacionesCursos.length : 0;
        const nuevaRecomendacionFecha = nuevosDatos.recomendacionesCursos && nuevosDatos.recomendacionesCursos.length > 0 
          ? nuevosDatos.recomendacionesCursos[0].updated_at 
          : '';
        
        const hayNuevosDatos = nuevosDatos.totalReportes > ultimoTotalReportes || 
                              nuevaFechaEvaluacion !== ultimaFechaEvaluacion ||
                              nuevosDatos.evaluaciones.length !== evaluacionesActuales.length ||
                              nuevosDatos.reportes.length !== reportesActuales.length;
        
        const hayCambioRecomendacion = nuevoTotalRecomendaciones !== ultimoTotalRecomendaciones || 
                                      nuevaRecomendacionFecha !== ultimaRecomendacionFecha;
        
        if (hayNuevosDatos) {
          console.log('Nuevos datos detectados, actualizando vista...');
          console.log('Total reportes anterior:', ultimoTotalReportes, '-> nuevo:', nuevosDatos.totalReportes);
          console.log('Evaluaciones anterior:', evaluacionesActuales.length, '-> nuevo:', nuevosDatos.evaluaciones.length);
          
          ultimoTotalReportes = nuevosDatos.totalReportes;
          evaluacionesActuales = nuevosDatos.evaluaciones;
          reportesActuales = nuevosDatos.reportes;
          ultimaFechaEvaluacion = nuevaFechaEvaluacion;
          
          // Actualizar paneles de evaluación
          actualizarPanelEvaluacionFisica(nuevosDatos.reporteMasReciente);
          actualizarPanelEvaluacionPsicologica(nuevosDatos.reporteMasReciente);
          
          // Actualizar secciones dinámicas si están visibles
          actualizarSeccionEncuestas(nuevosDatos.evaluaciones);
          actualizarSeccionHistorial();
          actualizarSeccionReportes();
          
          // Mostrar notificación de actualización
          mostrarNotificacionActualizacion();
        }
        
        // Actualizar recomendaciones de cursos si cambiaron
        if (hayCambioRecomendacion) {
          console.log('🤖 Cambio en recomendaciones detectado:', nuevoTotalRecomendaciones, 'recomendación(es)');
          console.log('Fecha anterior:', ultimaRecomendacionFecha, '-> Nueva:', nuevaRecomendacionFecha);
          
          ultimoTotalRecomendaciones = nuevoTotalRecomendaciones;
          ultimaRecomendacionFecha = nuevaRecomendacionFecha;
          recomendacionesActuales = nuevosDatos.recomendacionesCursos || [];
          
          // SIEMPRE actualizar la sección de cursos si está visible
          actualizarSeccionCursos(nuevosDatos.recomendacionesCursos);
          
          // Mostrar notificación global de cambio
          const mensaje = nuevoTotalRecomendaciones > 0 
            ? `🤖 ${nuevoTotalRecomendaciones} nueva(s) recomendación(es) de IA` 
            : '✓ Recomendaciones actualizadas';
          
          document.getElementById('toast-info-msg').textContent = mensaje;
          showToast('toast-info');
          setTimeout(() => hideToast('toast-info'), 4000);
        }
        
        // Verificar cambios en aptitud de necesidades
        const nuevaAptitudFecha = nuevosDatos.aptitudNecesidades ? nuevosDatos.aptitudNecesidades.updated_at : '';
        const hayCambioAptitud = nuevaAptitudFecha !== ultimaAptitudFecha;
        
        if (hayCambioAptitud) {
          console.log('Cambio en aptitud de necesidades detectado');
          ultimaAptitudFecha = nuevaAptitudFecha;
          aptitudActual = nuevosDatos.aptitudNecesidades;
          renderizarAptitudNecesidades();
        }
        
        // Actualizar reportes no vistos (para tags "Nueva")
        if (nuevosDatos.reportesNoVistos) {
          const reportesNoVistosJSON = JSON.stringify(reportesNoVistos);
          const nuevosReportesNoVistosJSON = JSON.stringify(nuevosDatos.reportesNoVistos);
          
          if (reportesNoVistosJSON !== nuevosReportesNoVistosJSON) {
            console.log('Cambio en reportes no vistos detectado');
            console.log('Reportes no vistos anteriores:', reportesNoVistos);
            console.log('Reportes no vistos nuevos:', nuevosDatos.reportesNoVistos);
            
            reportesNoVistos = nuevosDatos.reportesNoVistos;
            
            // Si estamos en la vista de encuestas, actualizarla
            const contenido = document.getElementById('vista-contenido');
            if (contenido && contenido.innerHTML.includes('Encuestas Realizadas')) {
              console.log('Re-renderizando encuestas con nuevos tags...');
              renderizarEncuestas();
            }
          }
        }
      }
    })
    .catch(error => {
      console.error('Error en polling:', error);
    });
  }

  // Función para actualizar la sección de historial si está visible
  function actualizarSeccionHistorial() {
    const contenido = document.getElementById('vista-contenido');
    if (!contenido) return;
    if (!contenido.innerHTML.includes('Historial') || contenido.innerHTML.includes('Encuestas')) return;
    
    console.log('Actualizando sección de historial');
    renderizarHistorial();
  }

  // Función para actualizar la sección de reportes si está visible
  function actualizarSeccionReportes() {
    const contenido = document.getElementById('vista-contenido');
    if (!contenido) return;
    if (!contenido.innerHTML.includes('<h2 class="titulo-seccion">Reportes</h2>')) return;
    
    console.log('Actualizando sección de reportes');
    renderizarReportes();
  }

  // Función para actualizar la sección de cursos si está visible
  function actualizarSeccionCursos(recomendaciones) {
    const contenido = document.getElementById('vista-contenido');
    if (!contenido) {
      console.log('⚠️ No se encontró vista-contenido');
      return;
    }
    
    // Verificar si estamos en la vista de cursos (más flexible)
    const enVistaCursos = contenido.innerHTML.includes('Cursos del Voluntario') || 
                          contenido.innerHTML.includes('recomendaciones-ia') ||
                          contenido.innerHTML.includes('btn-ver-curso');
    
    if (!enVistaCursos) {
      console.log('ℹ️ No estamos en la vista de cursos, no actualizando');
      return;
    }
    
    console.log('🔄 Actualizando recomendaciones de cursos en tiempo real');
    console.log('📊 Nuevas recomendaciones:', recomendaciones);
    
    // Actualizar variable global
    recomendacionesActuales = recomendaciones || [];
    
    // Renderizar nuevas recomendaciones
    renderizarRecomendaciones();
    
    console.log('✅ Recomendaciones renderizadas correctamente');
  }

  function actualizarPanelEvaluacionFisica(reporte) {
    const panel = document.getElementById('panel-evaluacion-fisica');
    if (!panel) return;
    
    let contenido = `
      <h4>
        <i class="fas fa-heartbeat"></i>
        Evaluaciones Físicas
      </h4>
    `;
    
    if (reporte && reporte.resumen_fisico) {
      const fecha = new Date(reporte.fecha_generado).toLocaleDateString('es-ES');
      contenido += `
        <div class="item-evaluacion">
          <i class="fas fa-file-alt"></i>
          <span>Última evaluación: ${fecha}</span>
        </div>
        <div class="item-evaluacion">
          <i class="fas fa-chart-line"></i>
          <span>Reporte #${reporte.id}</span>
        </div>
        <p>${reporte.resumen_fisico}</p>
      `;
    } else {
      contenido += `
        <div class="no-evaluacion">
          <i class="fas fa-file-alt icono-vacio"></i>
          <p>No hay evaluaciones físicas registradas.</p>
        </div>
      `;
    }
    
    panel.innerHTML = contenido;
  }

  function actualizarPanelEvaluacionPsicologica(reporte) {
    const panel = document.getElementById('panel-evaluacion-psicologica');
    if (!panel) return;
    
    let contenido = `
      <h4>
        <i class="fas fa-brain"></i>
        Evaluaciones Psicológicas
      </h4>
    `;
    
    if (reporte && reporte.resumen_emocional) {
      const fecha = new Date(reporte.fecha_generado).toLocaleDateString('es-ES');
      contenido += `
        <div class="item-evaluacion">
          <i class="fas fa-file-alt"></i>
          <span>Última evaluación: ${fecha}</span>
        </div>
        <div class="item-evaluacion">
          <i class="fas fa-chart-line"></i>
          <span>Reporte #${reporte.id}</span>
        </div>
        <p>${reporte.resumen_emocional}</p>
      `;
    } else {
      contenido += `
        <div class="no-evaluacion">
          <i class="fas fa-file-alt icono-vacio"></i>
          <p>No hay evaluaciones psicológicas registradas.</p>
        </div>
      `;
    }
    
    panel.innerHTML = contenido;
  }

  function mostrarNotificacionActualizacion() {
    hideAllToasts();
    document.getElementById('toast-success-msg').textContent = '¡Nuevos datos de evaluación recibidos!';
    showToast('toast-success');
    
    setTimeout(() => {
      hideToast('toast-success');
    }, 5000);
  }

  function actualizarSeccionEncuestas(evaluaciones) {
    // Actualizar la variable global
    evaluacionesActuales = evaluaciones;
    
    const contenido = document.getElementById('vista-contenido');
    if (!contenido) return;
    
    // Verificar si estamos en la vista de encuestas
    const enVistaEncuestas = contenido.innerHTML.includes('Encuestas Realizadas');
    if (!enVistaEncuestas) {
      console.log('No estamos en vista de encuestas, datos guardados para cuando se abra');
      return;
    }
    
    console.log('Actualizando sección de encuestas con', evaluaciones.length, 'evaluaciones');
    renderizarEncuestas();
  }


  // FUNCIONES PARA CERTIFICADOS
    let certificadoPendiente = {
      idUsuario: null,
      idCapacitacion: null,
      nombreCapacitacion: null,
      botonOriginal: null
    };

  function generarCertificado(idUsuario, idCapacitacion) {
    // Prevenir propagación del evento
    event.stopPropagation();
    
    // Guardar datos para cuando se confirme
    certificadoPendiente.idUsuario = idUsuario;
    certificadoPendiente.idCapacitacion = idCapacitacion;
    certificadoPendiente.botonOriginal = event.target;
    
    // Obtener nombre de la capacitación desde el DOM
    const cardCapacitacion = event.target.closest('.vista-card');
    const nombreCapacitacion = cardCapacitacion ? cardCapacitacion.querySelector('strong').textContent : 'esta capacitación';
    certificadoPendiente.nombreCapacitacion = nombreCapacitacion;
    
    // Actualizar el texto del modal
    document.getElementById('modal-capacitacion-nombre').textContent = nombreCapacitacion;
    
    // Mostrar el modal
    $('#modalConfirmarCertificado').modal('show');
  }

  /**
   * ✅ Ejecutar generación de certificado después de confirmación
   */
  function confirmarGenerarCertificado() {
    // Cerrar el modal
    $('#modalConfirmarCertificado').modal('hide');
    
    const idUsuario = certificadoPendiente.idUsuario;
    const idCapacitacion = certificadoPendiente.idCapacitacion;
    const btn = certificadoPendiente.botonOriginal;
    
    if (!btn) return;
    
    const textoOriginal = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';
    
    // Mostrar toast de loading
    hideAllToasts();
    showToast('toast-loading');

    fetch(`/certificados/generar/${idUsuario}/${idCapacitacion}`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    })
    .then(response => response.json())
    .then(data => {
      hideAllToasts();
      
      if (data.success) {
        document.getElementById('toast-success-msg').textContent = '¡Certificado generado y enviado por email!';
        showToast('toast-success');
        
        setTimeout(() => {
          hideToast('toast-success');
          location.reload(); // Recargar para mostrar botón "Ver Certificado"
        }, 2000);
      } else {
        document.getElementById('toast-error-msg').textContent = 'Error: ' + data.message;
        showToast('toast-error');
        btn.disabled = false;
        btn.innerHTML = textoOriginal;
      }
    })
    .catch(error => {
      console.error('Error:', error);
      hideAllToasts();
      document.getElementById('toast-error-msg').textContent = 'Error al generar certificado';
      showToast('toast-error');
      btn.disabled = false;
      btn.innerHTML = textoOriginal;
    });
  }



  function verCertificado(idUsuario, idCapacitacion) {
    // Obtener el ID del certificado
    fetch(`/api/certificados/${idUsuario}/${idCapacitacion}`)
      .then(response => response.json())
      .then(data => {
        if (data.success && data.certificado) {
          window.open(`/certificados/descargar/${data.certificado.id}`, '_blank');
        } else {
          hideAllToasts();
          document.getElementById('toast-error-msg').textContent = 'Certificado no encontrado';
          showToast('toast-error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        hideAllToasts();
        document.getElementById('toast-error-msg').textContent = 'Error al obtener certificado';
        showToast('toast-error');
      });
  }

  // Iniciar polling automático
  setInterval(actualizarDatosVoluntario, INTERVALO_POLLING);

  // También verificar al cargar la página
  document.addEventListener('DOMContentLoaded', function() {
    console.log('Polling de datos activado (cada ' + (INTERVALO_POLLING/1000) + ' segundos)');
    
    // Renderizar aptitud inicial si existe
    renderizarAptitudNecesidades();
  });


  </script>


  @endsection