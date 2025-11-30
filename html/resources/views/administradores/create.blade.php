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
            <div class="alert alert-danger">
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
                  <input type="text" class="form-control" id="nombre" name="nombre"
                         placeholder="Ingrese el nombre" maxlength="30"
                         value="{{ old('nombre') }}" required>
                  <small class="form-text text-muted">
                    <span id="contadorNombre">{{ strlen(old('nombre')) }}</span>/30 caracteres
                  </small>
                  <span class="error-message" id="errorNombre">
                      @error('nombre') {{ $message }} @enderror
                  </span>
                </div>
              </div>

              <!-- Apellido -->
              <div class="col-md-6">
                <div class="form-group">
                  <label for="apellido">Apellido <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="apellido" name="apellido"
                         placeholder="Ingrese el apellido" maxlength="30"
                         value="{{ old('apellido') }}" required>
                  <small class="form-text text-muted">
                    <span id="contadorApellido">{{ strlen(old('apellido')) }}</span>/30 caracteres
                  </small>
                  <span class="error-message" id="errorApellido">
                      @error('apellido') {{ $message }} @enderror
                  </span>
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
                    <input type="email" class="form-control" id="correo" name="correo"
                           placeholder="ejemplo@correo.com" maxlength="50"
                           value="{{ old('correo') }}" required>
                  </div>
                  <span class="error-message" id="errorCorreo">
                      @error('correo') {{ $message }} @enderror
                  </span>
                </div>
              </div>

              <!-- CI + Extensión -->
              <div class="col-md-6">
                <div class="form-group">
                  <label for="ci">Cédula de Identidad <span class="text-danger">*</span></label>
                  <div class="ci-inputs d-flex gap-2">
                    <input type="text" class="form-control" id="ci" name="ci"
                           placeholder="Ingrese el CI" maxlength="8"
                           value="{{ old('ci') }}" required>
                    <input type="text" class="form-control extension-input" id="extension"
                           name="extension" placeholder="Ext." maxlength="2"
                           value="{{ old('extension') }}">
                  </div>
                  <span class="error-message" id="errorCi">
                      @error('ci') {{ $message }} @enderror
                  </span>
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
                    <input type="text" class="form-control" id="telefono" name="telefono"
                           placeholder="Ingrese el teléfono" maxlength="8"
                           value="{{ old('telefono') }}">
                  </div>
                  <span class="error-message" id="errorTelefono">
                      @error('telefono') {{ $message }} @enderror
                  </span>
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
@endsection

@section('css')
<style>
  .form-group.has-error .form-control { border-color: #dc3545; }
  .error-message { color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem; }
  .ci-inputs { display: flex; gap: 10px; }
  .extension-input { width: 80px; }
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

  // Contadores
  inputs.nombre.addEventListener('input', () => {
    document.getElementById('contadorNombre').textContent = inputs.nombre.value.length;
    limpiarError('nombre');
  });
  inputs.apellido.addEventListener('input', () => {
    document.getElementById('contadorApellido').textContent = inputs.apellido.value.length;
    limpiarError('apellido');
  });

  // Solo números en CI / teléfono
  ['ci','telefono'].forEach(campo => {
    inputs[campo].addEventListener('input', function() {
      this.value = this.value.replace(/[^0-9]/g, '');
      limpiarError(campo);
    });
  });

  inputs.correo.addEventListener('input', () => limpiarError('correo'));

  function limpiarError(campo) {
    const span = document.getElementById('error' + campo.charAt(0).toUpperCase() + campo.slice(1));
    const input = inputs[campo];
    if (span) span.textContent = '';
    if (input) input.classList.remove('is-invalid');
  }

  function mostrarError(campo, msg) {
    const span = document.getElementById('error' + campo.charAt(0).toUpperCase() + campo.slice(1));
    const input = inputs[campo];
    if (span) span.textContent = msg;
    if (input) input.classList.add('is-invalid');
  }

  function validar() {
    let ok = true;

    if (!inputs.nombre.value.trim()) {
      mostrarError('nombre','El nombre es requerido'); ok = false;
    }
    if (!inputs.apellido.value.trim()) {
      mostrarError('apellido','El apellido es requerido'); ok = false;
    }
    if (!inputs.correo.value.trim()) {
      mostrarError('correo','El correo es requerido'); ok = false;
    } else if (!/\S+@\S+\.\S+/.test(inputs.correo.value)) {
      mostrarError('correo','Formato de correo inválido'); ok = false;
    }
    if (!inputs.ci.value.trim()) {
      mostrarError('ci','El CI es requerido'); ok = false;
    } else if (inputs.ci.value.length < 7 || inputs.ci.value.length > 8) {
      mostrarError('ci','El CI debe tener 7-8 dígitos'); ok = false;
    }
    if (inputs.telefono.value &&
        (inputs.telefono.value.length < 7 || inputs.telefono.value.length > 8)) {
      mostrarError('telefono','Teléfono inválido'); ok = false;
    }
    return ok;
  }

  form.addEventListener('submit', e => {
    if (!validar()) {
      e.preventDefault(); // solo cancela si hay errores de frontend
    }
  });
});
</script>
@endsection
