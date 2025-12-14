@extends('adminlte::page')

@section('title', 'Agregar Administrador')

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
</style>

<div class="container-fluid">
  <div class="form-container">

        <div class="form-header">
            <h1 class="form-titulo">Agregar Administrador</h1>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h5><i class="icon fas fa-ban"></i> Por favor, revisa los siguientes campos:</h5>
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

      <div class="card card-outline card-primary">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-user-cog"></i> Datos del Administrador
          </h3>
        </div>

        <form id="formAdmin" method="POST" action="{{ route('administradores.store') }}">
          @csrf
          <div class="card-body">
            <div class="row">
              <!-- Nombre -->
              <div class="col-md-6">
                <div class="form-group">
                  <label for="nombre">Nombre <span class="text-danger">*</span></label>
                  <input type="text" 
                         class="form-control @error('nombre') is-invalid @enderror" 
                         id="nombre" 
                         name="nombre"
                         placeholder="Ingrese el nombre" 
                         minlength="2"
                         maxlength="30"
                         value="{{ old('nombre') }}" 
                         required>
                  <small class="form-text text-muted">
                    <span id="contadorNombre">{{ strlen(old('nombre')) }}</span>/30 caracteres (mínimo 2)
                  </small>
                  @error('nombre')
                      <span class="invalid-feedback d-block">{{ $message }}</span>
                  @enderror
                </div>
              </div>

              <!-- Apellido -->
              <div class="col-md-6">
                <div class="form-group">
                  <label for="apellido">Apellido <span class="text-danger">*</span></label>
                  <input type="text" 
                         class="form-control @error('apellido') is-invalid @enderror" 
                         id="apellido" 
                         name="apellido"
                         placeholder="Ingrese el apellido" 
                         minlength="2"
                         maxlength="30"
                         value="{{ old('apellido') }}" 
                         required>
                  <small class="form-text text-muted">
                    <span id="contadorApellido">{{ strlen(old('apellido')) }}</span>/30 caracteres (mínimo 2)
                  </small>
                  @error('apellido')
                      <span class="invalid-feedback d-block">{{ $message }}</span>
                  @enderror
                </div>
              </div>

              <!-- Correo -->
              <div class="col-md-6">
                <div class="form-group">
                  <label for="correo">Correo Electrónico <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    </div>
                    <input type="email" 
                           class="form-control @error('correo') is-invalid @enderror" 
                           id="correo" 
                           name="correo"
                           placeholder="ejemplo@correo.com" 
                           maxlength="50"
                           value="{{ old('correo') }}" 
                           required>
                  </div>
                  <small class="form-text text-muted">Debe ser un correo válido</small>
                  @error('correo')
                      <span class="invalid-feedback d-block">{{ $message }}</span>
                  @enderror
                </div>
              </div>

              <!-- CI + Extensión -->
              <div class="col-md-6">
                <div class="form-group">
                  <label for="ci">Cédula de Identidad <span class="text-danger">*</span></label>
                  <div class="ci-inputs d-flex gap-2">
                    <input type="text" 
                           class="form-control @error('ci') is-invalid @enderror" 
                           id="ci" 
                           name="ci"
                           placeholder="Ingrese el CI" 
                           minlength="6"
                           maxlength="8"
                           value="{{ old('ci') }}" 
                           required>
                    <select class="form-control extension-input @error('extension') is-invalid @enderror" 
                            id="extension"
                            name="extension">
                      <option value="">Ext.</option>
                      <option value="LP" {{ old('extension')=='LP' ? 'selected' : '' }}>LP</option>
                      <option value="CB" {{ old('extension')=='CB' ? 'selected' : '' }}>CB</option>
                      <option value="SC" {{ old('extension')=='SC' ? 'selected' : '' }}>SC</option>
                      <option value="OR" {{ old('extension')=='OR' ? 'selected' : '' }}>OR</option>
                      <option value="PT" {{ old('extension')=='PT' ? 'selected' : '' }}>PT</option>
                      <option value="TJ" {{ old('extension')=='TJ' ? 'selected' : '' }}>TJ</option>
                      <option value="CH" {{ old('extension')=='CH' ? 'selected' : '' }}>CH</option>
                      <option value="BE" {{ old('extension')=='BE' ? 'selected' : '' }}>BE</option>
                      <option value="PD" {{ old('extension')=='PD' ? 'selected' : '' }}>PD</option>
                    </select>
                  </div>
                  <small class="form-text text-muted">6-8 dígitos, solo números</small>
                  @error('ci')
                      <span class="invalid-feedback d-block">{{ $message }}</span>
                  @enderror
                  @error('extension')
                      <span class="invalid-feedback d-block">{{ $message }}</span>
                  @enderror
                </div>
              </div>

              <!-- Teléfono -->
              <div class="col-md-6">
                <div class="form-group">
                  <label for="telefono">Teléfono</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-phone"></i></span>
                    </div>
                    <input type="text" 
                           class="form-control @error('telefono') is-invalid @enderror" 
                           id="telefono" 
                           name="telefono"
                           placeholder="Ej: 71234567" 
                           minlength="7"
                           maxlength="8"
                           value="{{ old('telefono') }}">
                  </div>
                  <small class="form-text text-muted">7-8 dígitos (opcional)</small>
                  @error('telefono')
                      <span class="invalid-feedback d-block">{{ $message }}</span>
                  @enderror
                </div>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="card-footer text-right">
            <a href="{{ route('administradores.index') }}" class="btn btn-default">
              <i class="fas fa-times"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Agregar Administrador
            </button>
          </div>
        </form>
      </div>
  </div>
</div>

{{-- Modal de validación --}}
<div class="modal fade modal-validacion" id="modalValidacion" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Error de validación
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-0" id="mensajeValidacion"></p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-close-modal" data-dismiss="modal">
                    Entendido
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
  .form-group.has-error .form-control { border-color: #dc3545; }
  .ci-inputs { display: flex; gap: 10px; }
  .extension-input { width: 100px !important; }
  .invalid-feedback { display: block; }
  
  /* Estilos para modal de validación */
  .modal-validacion .modal-content {
      border-radius: 8px;
      border: none;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
  }
  
  .modal-validacion .modal-header {
      background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
      color: white;
      border-radius: 8px 8px 0 0;
      padding: 15px 20px;
  }
  
  .modal-validacion .modal-title {
      font-weight: 600;
  }
  
  .modal-validacion .modal-body {
      padding: 20px;
  }
  
  .modal-validacion .btn-close-modal {
      background: #dc3545;
      border: none;
      color: white;
      padding: 8px 20px;
      border-radius: 4px;
      transition: all 0.3s;
  }
  
  .modal-validacion .btn-close-modal:hover {
      background: #c82333;
      transform: translateY(-1px);
      box-shadow: 0 2px 5px rgba(220, 53, 69, 0.3);
  }
</style>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('formAdmin');
  const inputs = {
    nombre: document.getElementById('nombre'),
    apellido: document.getElementById('apellido'),
    correo: document.getElementById('correo'),
    ci: document.getElementById('ci'),
    extension: document.getElementById('extension'),
    telefono: document.getElementById('telefono')
  };

  // Función para mostrar modal de validación
  function mostrarModalValidacion(mensaje) {
    document.getElementById('mensajeValidacion').textContent = mensaje;
    $('#modalValidacion').modal('show');
  }

  // Contadores con validación de caracteres
  inputs.nombre.addEventListener('input', function() {
    // Solo letras y espacios
    this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
    document.getElementById('contadorNombre').textContent = this.value.length;
    limpiarError('nombre');
  });

  inputs.apellido.addEventListener('input', function() {
    // Solo letras y espacios
    this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
    document.getElementById('contadorApellido').textContent = this.value.length;
    limpiarError('apellido');
  });

  // Solo números en CI
  inputs.ci.addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
    limpiarError('ci');
    
    // Validación de longitud
    if (this.value.length > 0 && this.value.length < 6) {
      mostrarErrorEnLinea('ci', 'El CI debe tener entre 6 y 8 dígitos');
    }
  });

  // Solo números en teléfono
  inputs.telefono.addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
    limpiarError('telefono');
    
    // Validación de longitud si tiene contenido
    if (this.value.length > 0 && this.value.length < 7) {
      mostrarErrorEnLinea('telefono', 'El teléfono debe tener 7 u 8 dígitos');
    }
  });

  // Validación de correo en tiempo real
  inputs.correo.addEventListener('blur', function() {
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (this.value && !emailPattern.test(this.value)) {
      mostrarErrorEnLinea('correo', 'Ingrese un correo electrónico válido');
      this.classList.add('is-invalid');
    } else {
      limpiarError('correo');
    }
  });

  inputs.correo.addEventListener('input', () => limpiarError('correo'));
  inputs.extension.addEventListener('change', () => limpiarError('extension'));

  function limpiarError(campo) {
    const input = inputs[campo];
    if (input) {
      input.classList.remove('is-invalid');
      const feedback = input.parentElement.querySelector('.invalid-feedback:not(.d-block)');
      if (feedback) feedback.remove();
    }
  }

  function mostrarErrorEnLinea(campo, msg) {
    const input = inputs[campo];
    if (input) {
      input.classList.add('is-invalid');
      
      // Buscar si ya existe un mensaje de error dinámico
      let feedback = input.parentElement.querySelector('.invalid-feedback:not(.d-block)');
      if (!feedback) {
        feedback = document.createElement('span');
        feedback.className = 'invalid-feedback';
        feedback.style.display = 'block';
        input.parentElement.appendChild(feedback);
      }
      feedback.textContent = msg;
    }
  }

  function validar() {
    let isValid = true;
    let mensajeError = 'Por favor completa correctamente todos los campos obligatorios';

    // Validar nombre
    if (!inputs.nombre.value.trim()) {
      mostrarErrorEnLinea('nombre', 'El nombre es requerido');
      isValid = false;
    } else if (inputs.nombre.value.trim().length < 2) {
      mostrarErrorEnLinea('nombre', 'El nombre debe tener al menos 2 caracteres');
      isValid = false;
    }

    // Validar apellido
    if (!inputs.apellido.value.trim()) {
      mostrarErrorEnLinea('apellido', 'El apellido es requerido');
      isValid = false;
    } else if (inputs.apellido.value.trim().length < 2) {
      mostrarErrorEnLinea('apellido', 'El apellido debe tener al menos 2 caracteres');
      isValid = false;
    }

    // Validar correo
    if (!inputs.correo.value.trim()) {
      mostrarErrorEnLinea('correo', 'El correo es requerido');
      isValid = false;
    } else {
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailPattern.test(inputs.correo.value)) {
        mostrarErrorEnLinea('correo', 'Formato de correo inválido');
        mensajeError = 'Por favor ingrese un correo electrónico válido';
        isValid = false;
      }
    }

    // Validar CI
    if (!inputs.ci.value.trim()) {
      mostrarErrorEnLinea('ci', 'El CI es requerido');
      isValid = false;
    } else if (inputs.ci.value.length < 6 || inputs.ci.value.length > 8) {
      mostrarErrorEnLinea('ci', 'El CI debe tener entre 6 y 8 dígitos');
      mensajeError = 'El CI debe tener entre 6 y 8 dígitos';
      isValid = false;
    }

    // Validar teléfono (solo si tiene valor)
    if (inputs.telefono.value && 
        (inputs.telefono.value.length < 7 || inputs.telefono.value.length > 8)) {
      mostrarErrorEnLinea('telefono', 'El teléfono debe tener 7 u 8 dígitos');
      mensajeError = 'El teléfono debe tener 7 u 8 dígitos';
      isValid = false;
    }

    // Mostrar modal si hay errores
    if (!isValid) {
      mostrarModalValidacion(mensajeError);
    }

    return isValid;
  }

  // Validación al enviar formulario
  form.addEventListener('submit', function(e) {
    if (!validar()) {
      e.preventDefault();
    }
  });

  // Limpiar errores al escribir
  Object.keys(inputs).forEach(campo => {
    if (inputs[campo]) {
      inputs[campo].addEventListener('focus', () => limpiarError(campo));
    }
  });
});

// ========== AUTOCOMPLETADO GATEWAY ==========
@php
    $gatewayLookupUrl = rtrim(env('GATEWAY_REGISTRO_SIMPLE_URL', ''), '/');
@endphp

@if($gatewayLookupUrl)
(function() {
    const ciInput       = document.getElementById('ci');
    const nombreInput   = document.getElementById('nombre');
    const apellidoInput = document.getElementById('apellido');
    const telefonoInput = document.getElementById('telefono');

    const lookupBaseUrl = @json($gatewayLookupUrl);

    if (!ciInput || !lookupBaseUrl) {
        return;
    }

    let lastLookupCi = null;
    let isFetching   = false;

    ciInput.addEventListener('blur', async function () {
        const ci = (ciInput.value || '').trim();

        // Evitar llamadas con CI muy corto o repetidas
        if (ci.length < 5 || ci === lastLookupCi || isFetching) {
            return;
        }

        lastLookupCi = ci;
        isFetching   = true;

        try {
            const url = `${lookupBaseUrl}/${encodeURIComponent(ci)}`;

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Client-System': 'SEGUIMIENTO_DE_VOLUNTARIOS',
                },
            });

            if (!response.ok) {
                console.warn('Gateway lookup failed with status', response.status);
                return;
            }

            const json = await response.json();

            if (!json.success || !json.found || !json.data) {
                return;
            }

            const data = json.data;

            // Solo rellenar campos vacíos
            if (nombreInput && !nombreInput.value.trim() && data.nombre) {
                nombreInput.value = data.nombre;
                document.getElementById('contadorNombre').textContent = data.nombre.length;
            }
            if (apellidoInput && !apellidoInput.value.trim() && data.apellido) {
                apellidoInput.value = data.apellido;
                document.getElementById('contadorApellido').textContent = data.apellido.length;
            }
            if (telefonoInput && !telefonoInput.value.trim() && data.telefono) {
                telefonoInput.value = data.telefono;
            }

        } catch (error) {
            console.error('Error llamando al gateway para autocompletar', error);
        } finally {
            isFetching = false;
        }
    });
})();
@endif
</script>
@endsection

