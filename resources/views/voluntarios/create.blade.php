@extends('adminlte::page')

@section('title', 'Registrar Voluntario')

@section('content_header')
    <h1>Registrar nuevo voluntario</h1>
@stop

@section('content')
<div class="container-fluid">
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h5><i class="icon fas fa-ban"></i> Por favor, revisa los siguientes campos:</h5>
            <ul class="mb-0 pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center position-relative step-progress">
                <div class="progress-line"></div>
                
                <div class="step-indicator" data-step="1">
                    <div class="step-circle">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="step-label">Datos personales</div>
                </div>
                
                <div class="step-indicator" data-step="2">
                    <div class="step-circle">
                        <i class="fas fa-address-card"></i>
                    </div>
                    <div class="step-label">Contacto y salud</div>
                </div>
                
                <div class="step-indicator" data-step="3">
                    <div class="step-circle">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="step-label">Confirmación</div>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('voluntarios.store') }}" id="form-voluntario">
        @csrf

        {{-- PASO 1: Datos personales --}}
        <div class="card card-primary card-outline step-card" data-step="1">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user mr-2"></i>Paso 1: Datos personales</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombres">Nombres <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="nombres" 
                                   id="nombres"
                                   class="form-control @error('nombres') is-invalid @enderror"
                                   value="{{ old('nombres') }}" 
                                   placeholder="Ingrese los nombres"
                                   required>
                            @error('nombres')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="apellidos">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="apellidos" 
                                   id="apellidos"
                                   class="form-control @error('apellidos') is-invalid @enderror"
                                   value="{{ old('apellidos') }}"
                                   placeholder="Ingrese los apellidos"
                                   required>
                            @error('apellidos')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ci">Carnet de Identidad <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="ci" 
                                   id="ci"
                                   class="form-control @error('ci') is-invalid @enderror"
                                   value="{{ old('ci') }}"
                                   placeholder="Ej: 1234567 LP"
                                   required>
                            @error('ci')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha_nacimiento">Fecha de nacimiento</label>
                            <input type="date" 
                                   name="fecha_nacimiento" 
                                   id="fecha_nacimiento"
                                   class="form-control @error('fecha_nacimiento') is-invalid @enderror"
                                   value="{{ old('fecha_nacimiento') }}">
                            @error('fecha_nacimiento')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="genero">Género</label>
                            <select name="genero" 
                                    id="genero"
                                    class="form-control select2 @error('genero') is-invalid @enderror">
                                <option value="">Seleccionar...</option>
                                <option value="Masculino" {{ old('genero')=='Masculino' ? 'selected' : '' }}>Masculino</option>
                                <option value="Femenino" {{ old('genero')=='Femenino' ? 'selected' : '' }}>Femenino</option>
                                <option value="Otro" {{ old('genero')=='Otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                            @error('genero')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- PASO 2: Contacto y salud --}}
        <div class="card card-primary card-outline step-card" data-step="2" style="display:none;">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-address-card mr-2"></i>Paso 2: Contacto y datos operativos</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                </div>
                                <input type="text" 
                                       name="telefono" 
                                       id="telefono"
                                       class="form-control @error('telefono') is-invalid @enderror"
                                       value="{{ old('telefono') }}"
                                       placeholder="Ej: 71234567">
                                @error('telefono')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email (acceso a la app) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                </div>
                                <input type="email" 
                                       name="email" 
                                       id="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}"
                                       placeholder="ejemplo@correo.com"
                                       required>
                                @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="direccion_domicilio">Dirección de domicilio</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                </div>
                                <input type="text" 
                                       name="direccion_domicilio" 
                                       id="direccion_domicilio"
                                       class="form-control @error('direccion_domicilio') is-invalid @enderror"
                                       value="{{ old('direccion_domicilio') }}"
                                       placeholder="Ingrese la dirección completa">
                                @error('direccion_domicilio')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <select name="estado" 
                                    id="estado"
                                    class="form-control select2 @error('estado') is-invalid @enderror">
                                <option value="activo" {{ old('estado','activo')=='activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ old('estado')=='inactivo' ? 'selected' : '' }}>Inactivo</option>
                                <option value="baja" {{ old('estado')=='baja' ? 'selected' : '' }}>De baja</option>
                            </select>
                            @error('estado')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="nivel_entrenamiento">Nivel de entrenamiento</label>
                            <select name="nivel_entrenamiento" 
                                    id="nivel_entrenamiento"
                                    class="form-control select2 @error('nivel_entrenamiento') is-invalid @enderror">
                                <option value="">Seleccionar...</option>
                                <option value="Bajo" {{ old('nivel_entrenamiento')=='Bajo' ? 'selected' : '' }}>Bajo</option>
                                <option value="Medio" {{ old('nivel_entrenamiento')=='Medio' ? 'selected' : '' }}>Medio</option>
                                <option value="Alto" {{ old('nivel_entrenamiento')=='Alto' ? 'selected' : '' }}>Alto</option>
                            </select>
                            @error('nivel_entrenamiento')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="tipo_sangre">Tipo de sangre</label>
                            <select name="tipo_sangre" 
                                    id="tipo_sangre"
                                    class="form-control select2 @error('tipo_sangre') is-invalid @enderror">
                                <option value="">Seleccionar...</option>
                                <option value="A+" {{ old('tipo_sangre')=='A+' ? 'selected' : '' }}>A+</option>
                                <option value="A-" {{ old('tipo_sangre')=='A-' ? 'selected' : '' }}>A-</option>
                                <option value="B+" {{ old('tipo_sangre')=='B+' ? 'selected' : '' }}>B+</option>
                                <option value="B-" {{ old('tipo_sangre')=='B-' ? 'selected' : '' }}>B-</option>
                                <option value="AB+" {{ old('tipo_sangre')=='AB+' ? 'selected' : '' }}>AB+</option>
                                <option value="AB-" {{ old('tipo_sangre')=='AB-' ? 'selected' : '' }}>AB-</option>
                                <option value="O+" {{ old('tipo_sangre')=='O+' ? 'selected' : '' }}>O+</option>
                                <option value="O-" {{ old('tipo_sangre')=='O-' ? 'selected' : '' }}>O-</option>
                            </select>
                            @error('tipo_sangre')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="entidad_pertenencia">Entidad de pertenencia</label>
                            <input type="text" 
                                   name="entidad_pertenencia" 
                                   id="entidad_pertenencia"
                                   class="form-control @error('entidad_pertenencia') is-invalid @enderror"
                                   value="{{ old('entidad_pertenencia') }}"
                                   placeholder="Ej: Cruz Roja Boliviana">
                            @error('entidad_pertenencia')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- PASO 3: Confirmación --}}
        <div class="card card-success card-outline step-card" data-step="3" style="display:none;">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-check mr-2"></i>Paso 3: Confirmación</h3>
            </div>
            <div class="card-body">
                <div class="callout callout-info">
                    <h5><i class="fas fa-info-circle"></i> ¿Qué sucederá al guardar?</h5>
                    <ul class="mb-0">
                        <li>Se creará el voluntario con el estado seleccionado.</li>
                        <li>Se generará un historial clínico vacío para el voluntario.</li>
                        <li>Se enviará un correo electrónico a <strong><span id="email-preview"></span></strong> para que configure su contraseña y pueda acceder a la aplicación móvil.</li>
                    </ul>
                </div>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Por favor, revisa todos los datos antes de continuar. Una vez guardado, el voluntario recibirá un correo de configuración.
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-default" id="btn-prev">
                        <i class="fas fa-arrow-left"></i> Anterior
                    </button>
                    <div>
                        <a href="{{ route('voluntarios.index') }}" class="btn btn-secondary">
                            Cancelar
                        </a>
                        <button type="button" class="btn btn-primary" id="btn-next">
                            Siguiente <i class="fas fa-arrow-right"></i>
                        </button>
                        <button type="submit" class="btn btn-success" id="btn-submit" style="display:none;">
                            <i class="fas fa-save"></i> Guardar voluntario
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

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
</div>
@stop

@section('css')
<style>
    .step-progress {
        padding: 0 50px;
    }
    
    .progress-line {
        position: absolute;
        top: 20px;
        left: 50px;
        right: 50px;
        height: 2px;
        background: #dee2e6;
        z-index: 0;
    }
    
    .step-indicator {
        text-align: center;
        position: relative;
        z-index: 1;
        flex: 1;
    }
    
    .step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 3px solid #dee2e6;
        background: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 8px;
        font-size: 16px;
        color: #6c757d;
        transition: all 0.3s ease;
    }
    
    .step-indicator.active .step-circle {
        background: #007bff;
        border-color: #007bff;
        color: #fff;
        box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.2);
    }
    
    .step-indicator.completed .step-circle {
        background: #28a745;
        border-color: #28a745;
        color: #fff;
    }
    
    .step-label {
        font-size: 0.875rem;
        color: #6c757d;
        font-weight: 500;
    }
    
    .step-indicator.active .step-label {
        color: #007bff;
        font-weight: 600;
    }
    
    .step-indicator.completed .step-label {
        color: #28a745;
    }
    
    .step-card {
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    .invalid-feedback {
        display: block;
    }
    
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
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
    }
    
    let currentStep = 1;
    const totalSteps = 3;

    const steps = document.querySelectorAll('.step-card');
    const indicators = document.querySelectorAll('.step-indicator');
    const btnPrev = document.getElementById('btn-prev');
    const btnNext = document.getElementById('btn-next');
    const btnSubmit = document.getElementById('btn-submit');

    function mostrarModalValidacion(mensaje) {
        document.getElementById('mensajeValidacion').textContent = mensaje;
        $('#modalValidacion').modal('show');
    }

    function validarFechaNacimiento(fecha) {
        if (!fecha) return { valido: true };

        const fechaNac = new Date(fecha);
        const hoy = new Date();
        
        hoy.setHours(0, 0, 0, 0);
        fechaNac.setHours(0, 0, 0, 0);

        if (fechaNac > hoy) {
            return {
                valido: false,
                mensaje: 'La fecha de nacimiento no puede ser posterior a la fecha actual.'
            };
        }

        let edad = hoy.getFullYear() - fechaNac.getFullYear();
        const mes = hoy.getMonth() - fechaNac.getMonth();
        
        if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNac.getDate())) {
            edad--;
        }

        // Validar edad mínima de 18 años
        if (edad < 18) {
            return {
                valido: false,
                mensaje: 'El voluntario debe ser mayor de 18 años. Edad calculada: ' + edad + ' años.'
            };
        }

        return { valido: true };
    }

    function renderSteps() {
        // Ocultar todos los pasos
        steps.forEach(step => {
            step.style.display = 'none';
        });
        
        const currentCard = document.querySelector(`.step-card[data-step="${currentStep}"]`);
        if (currentCard) {
            currentCard.style.display = 'block';
        }

        // Actualizar indicadores
        indicators.forEach(indicator => {
            const stepNum = parseInt(indicator.getAttribute('data-step'));
            indicator.classList.remove('active', 'completed');
            
            if (stepNum === currentStep) {
                indicator.classList.add('active');
            } else if (stepNum < currentStep) {
                indicator.classList.add('completed');
            }
        });

        // Actualizar botones
        btnPrev.style.display = currentStep > 1 ? 'inline-block' : 'none';
        btnNext.style.display = currentStep < totalSteps ? 'inline-block' : 'none';
        btnSubmit.style.display = currentStep === totalSteps ? 'inline-block' : 'none';
        
        // Scroll suave al inicio del formulario
        const stepProgress = document.querySelector('.step-progress');
        if (stepProgress) {
            window.scrollTo({
                top: stepProgress.offsetTop - 100,
                behavior: 'smooth'
            });
        }
    }

    // Validar paso antes de avanzar
    function validateCurrentStep() {
        const currentCard = document.querySelector(`.step-card[data-step="${currentStep}"]`);
        const requiredInputs = currentCard.querySelectorAll('[required]');
        let isValid = true;
        let mensajeError = 'Por favor completa todos los campos obligatorios';

        requiredInputs.forEach(input => {
            const value = input.value.trim();
            
            if (!value) {
                isValid = false;
                input.classList.add('is-invalid');
                
                let errorMsg = input.nextElementSibling;
                if (!errorMsg || !errorMsg.classList.contains('invalid-feedback')) {
                    const span = document.createElement('span');
                    span.className = 'invalid-feedback';
                    span.textContent = 'Este campo es obligatorio';
                    input.parentNode.insertBefore(span, input.nextSibling);
                }
            } else {
                input.classList.remove('is-invalid');
                const errorMsg = input.nextElementSibling;
                if (errorMsg && errorMsg.classList.contains('invalid-feedback')) {
                    errorMsg.remove();
                }
            }
        });

        // Validación especial para fecha de nacimiento (Paso 1)
        if (currentStep === 1) {
            const fechaNacInput = document.getElementById('fecha_nacimiento');
            if (fechaNacInput && fechaNacInput.value) {
                const resultadoValidacion = validarFechaNacimiento(fechaNacInput.value);
                
                if (!resultadoValidacion.valido) {
                    isValid = false;
                    mensajeError = resultadoValidacion.mensaje;
                    fechaNacInput.classList.add('is-invalid');
                    
                    let errorMsg = fechaNacInput.nextElementSibling;
                    if (!errorMsg || !errorMsg.classList.contains('invalid-feedback')) {
                        const span = document.createElement('span');
                        span.className = 'invalid-feedback';
                        span.textContent = resultadoValidacion.mensaje;
                        fechaNacInput.parentNode.insertBefore(span, fechaNacInput.nextSibling);
                    } else {
                        errorMsg.textContent = resultadoValidacion.mensaje;
                    }
                }
            }
        }

        // Validación especial para email (Paso 2)
        if (currentStep === 2) {
            const emailInput = document.getElementById('email');
            if (emailInput && emailInput.value) {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(emailInput.value)) {
                    isValid = false;
                    mensajeError = 'Por favor ingrese un correo electrónico válido';
                    emailInput.classList.add('is-invalid');
                    
                    let errorMsg = emailInput.nextElementSibling;
                    if (!errorMsg || !errorMsg.classList.contains('invalid-feedback')) {
                        const span = document.createElement('span');
                        span.className = 'invalid-feedback';
                        span.textContent = 'Ingrese un email válido';
                        emailInput.parentNode.insertBefore(span, emailInput.nextSibling);
                    }
                }
            }
        }

        // Mostrar modal si hay errores
        if (!isValid) {
            mostrarModalValidacion(mensajeError);
        }

        return isValid;
    }

    // Actualizar preview del email en paso 3
    function updateEmailPreview() {
        const emailInput = document.getElementById('email');
        const emailPreview = document.getElementById('email-preview');
        
        if (emailInput && emailPreview) {
            emailPreview.textContent = emailInput.value || 'no especificado';
        }
    }

    // Event listeners
    btnPrev.addEventListener('click', function() {
        if (currentStep > 1) {
            currentStep--;
            renderSteps();
        }
    });

    btnNext.addEventListener('click', function() {
        if (validateCurrentStep() && currentStep < totalSteps) {
            currentStep++;
            
            if (currentStep === 3) {
                updateEmailPreview();
            }
            renderSteps();
        }
    });

    // Validación en tiempo real para fecha de nacimiento
    const fechaNacInput = document.getElementById('fecha_nacimiento');
    if (fechaNacInput) {
        fechaNacInput.addEventListener('change', function() {
            const resultadoValidacion = validarFechaNacimiento(this.value);
            
            if (!resultadoValidacion.valido) {
                this.classList.add('is-invalid');
                
                let errorMsg = this.nextElementSibling;
                if (!errorMsg || !errorMsg.classList.contains('invalid-feedback')) {
                    const span = document.createElement('span');
                    span.className = 'invalid-feedback';
                    span.textContent = resultadoValidacion.mensaje;
                    this.parentNode.insertBefore(span, this.nextSibling);
                } else {
                    errorMsg.textContent = resultadoValidacion.mensaje;
                }
            } else {
                this.classList.remove('is-invalid');
                const errorMsg = this.nextElementSibling;
                if (errorMsg && errorMsg.classList.contains('invalid-feedback')) {
                    errorMsg.remove();
                }
            }
        });
    }

    // Validación en tiempo real para otros inputs
    const allInputs = document.querySelectorAll('input[required], select[required]');
    allInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim()) {
                this.classList.remove('is-invalid');
                const errorMsg = this.nextElementSibling;
                if (errorMsg && errorMsg.classList.contains('invalid-feedback')) {
                    errorMsg.remove();
                }
            }
        });
        
        input.addEventListener('change', function() {
            if (this.value.trim()) {
                this.classList.remove('is-invalid');
                const errorMsg = this.nextElementSibling;
                if (errorMsg && errorMsg.classList.contains('invalid-feedback')) {
                    errorMsg.remove();
                }
            }
        });
    });

    // Validación del formulario antes de enviar
    const form = document.getElementById('form-voluntario');
    form.addEventListener('submit', function(e) {
        if (!validateCurrentStep()) {
            e.preventDefault();
        }
    });

    // Inicializar
    renderSteps();

    // ========== AUTOCOMPLETADO GATEWAY ==========
    @php
        $gatewayLookupUrl = rtrim(env('GATEWAY_REGISTRO_SIMPLE_URL', ''), '/');
    @endphp

    @if($gatewayLookupUrl)
    (function() {
        const ciInput       = document.getElementById('ci');
        const nombreInput   = document.getElementById('nombres');    // 'nombres' en voluntarios
        const apellidoInput = document.getElementById('apellidos');  // 'apellidos' en voluntarios
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
                }
                if (apellidoInput && !apellidoInput.value.trim() && data.apellido) {
                    apellidoInput.value = data.apellido;
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
});
</script>
@stop