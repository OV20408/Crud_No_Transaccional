@extends('adminlte::page')
@section('title', 'Crear capacitación')

<style>
  /* Multi-step stepper */
  .bs-stepper-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 0;
  }

  .step {
    flex: 1;
    text-align: center;
  }

  .step-trigger {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    background: none;
    border: none;
    cursor: pointer;
    padding: 10px;
    transition: all 0.3s ease;
  }

  .step-trigger:focus {
    outline: none;
  }

  .bs-stepper-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 16px;
    margin-bottom: 8px;
    transition: all 0.3s ease;
  }

  .step.active .bs-stepper-circle {
    background-color: #007bff;
    color: white;
  }

  .bs-stepper-label {
    font-size: 13px;
    color: #6c757d;
    font-weight: 500;
  }

  .step.active .bs-stepper-label {
    color: #007bff;
    font-weight: 600;
  }

  .line {
    flex: 1;
    height: 2px;
    background-color: #e9ecef;
    margin: 0 10px;
    align-self: center;
    margin-bottom: 30px;
  }

  .step.active~.step .line {
    background-color: #e9ecef;
  }

  .bs-stepper-content .content {
    display: none;
  }

  .bs-stepper-content .content.active {
    display: block;
  }

  /* Cards de cursos */
  .curso-card {
    background-color: #fff;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 15px;
    transition: all 0.3s ease;
  }

  .curso-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    border-color: #007bff;
  }

  .curso-card-header {
    font-weight: 600;
    font-size: 16px;
    color: #333;
    margin-bottom: 10px;
  }

  .curso-card-body {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .etapas-count {
    color: #007bff;
    font-weight: 500;
    font-size: 14px;
  }

  /* Items de etapas */
  .step-item {
    display: flex;
    align-items: center;
    padding: 15px;
    margin-bottom: 15px;
    background-color: #f8f9fa;
    border-left: 4px solid #007bff;
    border-radius: 4px;
    transition: all 0.3s ease;
  }

  .step-item:hover {
    background-color: #e9ecef;
    transform: translateX(5px);
  }

  .step-number {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background-color: #007bff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
    margin-right: 15px;
    flex-shrink: 0;
  }

  .step-content {
    flex: 1;
  }

  .step-title {
    font-weight: 600;
    color: #007bff;
    margin-bottom: 5px;
    font-size: 14px;
  }

  .step-description {
    color: #495057;
    font-size: 14px;
  }

  .step-actions {
    opacity: 0;
    transition: opacity 0.3s ease;
    flex-shrink: 0;
  }

  .step-item:hover .step-actions {
    opacity: 1;
  }

  .modal-header.bg-primary {
    background-color: #007bff !important;
    color: white;
  }
</style>



<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<!-- Modal de error Cross-checking -->
<!-- <div class="modal fade" id="modalErrorCross" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Conflicto detectado</h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <p id="modalErrorCrossMensaje" class="mb-0"></p>
      </div>

      <div class="modal-footer">
        <button class="btn btn-danger" data-dismiss="modal">Entendido</button>
      </div>

    </div>
  </div>
</div> -->


@section('content')
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Nueva capacitación</h3>
    </div>

    <form action="{{ route('capacitaciones.store') }}" method="POST" id="formCapacitacion">
      @csrf

      {{-- aquí se inyectarán los inputs ocultos de cursos/etapas --}}
      <div id="cursosHiddenInputs"></div>

      <div class="card-body">
        <div id="noCoursesMessage" class="text-danger d-none text-center w-100 mb-3" style="font-weight:600;">
          <i class="bi bi-exclamation-circle-fill" style="margin-right:6px;"></i>
            No se puede crear capacitación sin cursos
        </div>
        <div class="form-group">
          <label for="nombre">Nombre</label>
          <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre') }}" required>
          @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
          <label for="descripcion">Descripción</label>
          <input type="text" name="descripcion" id="descripcion" class="form-control" value="{{ old('descripcion') }}">
          @error('descripcion') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

      </div>

      <div class="card-footer">
        <a href="{{ route('capacitaciones.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="button" class="btn btn-primary" id="btnSubmitCapacitacion">Guardar</button>

        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalGestionarCursos">
          <i class="bi bi-list-ul"></i> Gestionar Cursos
        </button>
      </div>

    </form>
  </div>

  <!-- Modal Gestionar Cursos -->

  <!-- Modal Gestionar Cursos - Multi Step Form -->
  <div class="modal fade" id="modalGestionarCursos" tabindex="-1" role="dialog"
    aria-labelledby="modalGestionarCursosLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h4 class="modal-title" id="modalGestionarCursosLabel">
            <i class="bi bi-fire"></i> Cursos para: <span id="capacitacionNombre">Nueva Capacitación</span>
          </h4>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div id="alertErrorCross" class="alert alert-danger d-none" role="alert"></div>


          <!-- Indicador de pasos -->
          <div class="bs-stepper-header mb-4" role="tablist">
            <div class="step" data-target="#step-lista">
              <button type="button" class="step-trigger" role="tab" id="trigger-lista">
                <span class="bs-stepper-circle">1</span>
                <span class="bs-stepper-label">Lista de Cursos</span>
              </button>
            </div>
            <div class="line"></div>
            <div class="step" data-target="#step-formulario">
              <button type="button" class="step-trigger" role="tab" id="trigger-formulario">
                <span class="bs-stepper-circle">2</span>
                <span class="bs-stepper-label">Datos del Curso</span>
              </button>
            </div>
            <div class="line"></div>
            <div class="step" data-target="#step-etapas">
              <button type="button" class="step-trigger" role="tab" id="trigger-etapas">
                <span class="bs-stepper-circle">3</span>
                <span class="bs-stepper-label">Ver Etapas</span>
              </button>
            </div>
          </div>

          <!-- Contenido de pasos -->
          <div class="bs-stepper-content">

            <!-- PASO 1: Lista de Cursos -->
            <div id="step-lista" class="content active" role="tabpanel">
              <div id="cursosListaContainer">
                <div class="text-center py-5" id="noCursosMessage">
                  <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                  <p class="text-muted mt-3">No hay cursos agregados</p>
                  <button type="button" class="btn btn-primary" onclick="irAPaso(2)">
                    <i class="bi bi-plus-circle"></i> Agregar Primer Curso
                  </button>
                </div>

                <div id="cursosGrid" style="display: none;">
                  <h5 class="mb-3">Cursos agregados</h5>
                  <div class="row" id="cursosCards"></div>
                  <div class="text-right mt-3">
                    <button type="button" class="btn btn-primary" onclick="irAPaso(2)">
                      <i class="bi bi-plus-circle"></i> Agregar Nuevo Curso
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- PASO 2: Formulario Curso -->
            <div id="step-formulario" class="content" role="tabpanel">
              <div class="card card-outline card-primary">
                <div class="card-body">
                  <input type="hidden" id="cursoEditIndex" value="">

                  <div class="form-group">
                    <label for="cursoNombre">
                      Nombre del Curso <span class="text-danger">*</span>
                      <span class="text-muted small">(<span id="cursoNombreCount">0</span>/100)</span>
                    </label>
                    <input type="text" class="form-control" id="cursoNombre" maxlength="100"
                      placeholder="Ej: Primeros Auxilios Básicos">
                  </div>

                  <div class="form-group">
                    <label for="cursoDescripcion">
                      Descripción del Curso
                      <span class="text-muted small">(<span id="cursoDescripcionCount">0</span>/250)</span>
                    </label>
                    <textarea class="form-control" id="cursoDescripcion" rows="3" maxlength="250"
                      placeholder="Describe brevemente el contenido del curso"></textarea>
                  </div>

                  <hr>

                  <div class="form-group">
                    <label>Etapas del Curso <span class="text-danger">*</span> <span class="text-muted">(mínimo
                        3)</span></label>
                    <div class="input-group mb-3">
                      <input type="text" class="form-control" id="etapaNombre" maxlength="80"
                        placeholder="Ej: Evaluación inicial del paciente">
                      <input type="hidden" id="etapaEditIndex" value="">
                      <div class="input-group-append">
                        <button class="btn btn-primary" type="button" onclick="agregarEtapa()">
                          <i class="bi bi-plus-circle"></i> <span id="btnEtapaTexto">Agregar</span>
                        </button>
                      </div>
                    </div>

                    <small class="text-muted">
                      <span id="etapaCount">0</span>/80 caracteres
                    </small>

                    <div class="mt-3">
                      <h6>Etapas actuales <span class="badge badge-info" id="etapasBadge">0</span></h6>
                      <div id="etapasListContainer">
                        <p class="text-muted" id="noEtapasMessage">No hay etapas agregadas. Agrega al menos 3 etapas.</p>
                      </div>
                    </div>
                  </div>

                </div>
                <div class="card-footer">
                  <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" onclick="irAPaso(1)">
                      <i class="bi bi-arrow-left"></i> Volver
                    </button>

                    <button type="button" class="btn btn-primary" id="btnGuardarCurso" onclick="guardarCurso()" disabled>
                      <i class="bi bi-check-circle"></i> <span id="btnGuardarTexto">Guardar Curso</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- PASO 3: Ver Etapas -->
            <div id="step-etapas" class="content" role="tabpanel">
              <div class="card card-outline card-info">
                <div class="card-header">
                  <h4 class="card-title mb-0">
                    <i class="bi bi-list-check"></i> <span id="cursoEtapasTitulo">Curso</span>
                  </h4>
                </div>
                <div class="card-body">
                  <div id="etapasViewContainer">
                    <p class="text-muted">No hay etapas para mostrar</p>
                  </div>
                </div>
                <div class="card-footer">
                  <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" onclick="irAPaso(1)">
                      <i class="bi bi-arrow-left"></i> Volver a la lista
                    </button>
                    <div>
                      <button type="button" class="btn btn-outline-primary mr-2" onclick="editarCursoDesdeEtapas()">
                        <i class="bi bi-pencil"></i> Editar Curso
                      </button>
                      <button type="button" class="btn btn-success" onclick="irAPaso(1)">
                        <i class="bi bi-check-circle"></i> Finalizar
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="bi bi-x-circle"></i> Cerrar
          </button>
        </div>

      </div>
    </div>
  </div>



  <style>
    .modal-header.bg-primary {
      background-color: #007bff !important;
      color: white;
    }

    .curso-card {
      background-color: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 15px;
      transition: all 0.3s ease;
    }

    .curso-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .curso-card-header {
      font-weight: 600;
      color: #333;
      margin-bottom: 10px;
    }

    .curso-card-body {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .etapas-count {
      color: #007bff;
      font-weight: 500;
    }

    .step-item {
      display: flex;
      align-items: center;
      padding: 15px;
      margin-bottom: 15px;
      background-color: white;
      border: 1px solid #dee2e6;
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .step-item:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .step-number {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background-color: #007bff;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      font-size: 14px;
      margin-right: 15px;
    }

    .step-content {
      flex: 1;
    }

    .step-title {
      font-weight: bold;
      color: #007bff;
      margin-bottom: 5px;
    }

    .step-description {
      color: #333;
    }

    .step-actions {
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .step-item:hover .step-actions {
      opacity: 1;
    }
  </style>

  <script>



    function mostrarErrorCross(mensaje) {
      const alert = document.getElementById("alertErrorCross");
      if (!alert) return;

      alert.innerHTML = mensaje;
      alert.classList.remove("d-none");

      // Opcional: hacer scroll al inicio del modal
      alert.scrollIntoView({ behavior: "smooth", block: "start" });
    }



    async function validarCursoCrossChecking(nombre) {
      // Si no hay URL configurada, no hacemos nada y dejamos continuar
      if (!INCENDIOS_URL) {
        return false;
      }

      try {
        const url = `${INCENDIOS_URL}/sync/cursos/search?nombre=` + encodeURIComponent(nombre);

        const resp = await fetch(url, {
          headers: {
            "Authorization": "Bearer {{ env('INCENDIOS_TOKEN') }}",
            "Accept": "application/json"
          }
        });

        // Si el otro microservicio responde mal → tratamos como error de red
        if (!resp.ok) {
          throw new Error("No se pudo conectar con el otro sistema.");
        }

        const data = await resp.json();

        // true = existe en INCENDIOS, false = NO existe
        return data.exists === true;

      } catch (error) {
        // ⚠️ Solo avisamos, pero NO bloqueamos la creación
        mostrarErrorCross(
          `No se pudo validar la existencia del curso en el otro sistema.<br><small>${error.message}</small>`
        );

        // Muy importante: NO lanzamos error, devolvemos false
        return false;
      }
    }







    window.addEventListener('load', function () {
      let cursos = [];
      let etapasTemp = [];
      let cursoEditandoIndex = null;
      let pasoActual = 1;

      // Función para cambiar de paso
      window.irAPaso = function (paso) {
        // Ocultar todos los pasos
        document.querySelectorAll('.bs-stepper-content .content').forEach(function (content) {
          content.classList.remove('active');
        });

        // Remover clase active de todos los steps
        document.querySelectorAll('.step').forEach(function (step) {
          step.classList.remove('active');
        });

        // Mostrar paso actual
        document.getElementById('step-' + getStepName(paso)).classList.add('active');
        document.querySelector('.step[data-target="#step-' + getStepName(paso) + '"]').classList.add('active');

        // Si va al paso 2 (formulario), resetear o cargar datos
        if (paso === 2 && cursoEditandoIndex === null) {
          resetearFormularioCurso();
        }

        pasoActual = paso;
      }

      function showNoCourses() {
        const el = document.getElementById('noCoursesMessage');
        if (el) el.classList.remove('d-none');
      }

      function hideNoCourses() {
        const el = document.getElementById('noCoursesMessage');
        if (el) el.classList.add('d-none');
      }

      function getStepName(paso) {
        switch (paso) {
          case 1: return 'lista';
          case 2: return 'formulario';
          case 3: return 'etapas';
          default: return 'lista';
        }
      }

      function resetearFormularioCurso() {
        cursoEditandoIndex = null;
        document.getElementById('cursoEditIndex').value = '';
        document.getElementById('cursoNombre').value = '';
        document.getElementById('cursoDescripcion').value = '';
        document.getElementById('cursoNombreCount').textContent = '0';
        document.getElementById('cursoDescripcionCount').textContent = '0';

        etapasTemp = [];
        document.getElementById('etapaNombre').value = '';
        document.getElementById('etapaEditIndex').value = '';
        document.getElementById('etapaCount').textContent = '0';

        document.getElementById('btnGuardarTexto').textContent = 'Guardar Curso';
        document.getElementById('etapasBadge').textContent = '0';

        renderizarEtapas();
      }

      // Actualizar título del modal con nombre de capacitación
      const nombreCapInput = document.getElementById('nombre');
      const capNombreSpan = document.getElementById('capacitacionNombre');
      if (nombreCapInput) {
        nombreCapInput.addEventListener('input', function () {
          capNombreSpan.textContent = this.value.trim() || 'Nueva Capacitación';
        });
      }

      // Contadores de caracteres
      document.getElementById('cursoNombre').addEventListener('input', function () {
        document.getElementById('cursoNombreCount').textContent = this.value.length;
        validarFormularioCurso();
      });

      document.getElementById('cursoDescripcion').addEventListener('input', function () {
        document.getElementById('cursoDescripcionCount').textContent = this.value.length;
      });

      document.getElementById('etapaNombre').addEventListener('input', function () {
        document.getElementById('etapaCount').textContent = this.value.length;
      });

      // Agregar etapa
      window.agregarEtapa = function () {
        const etapaNombre = document.getElementById('etapaNombre').value.trim();
        const etapaEditIndex = document.getElementById('etapaEditIndex').value;

        if (!etapaNombre) return;

        if (etapaEditIndex !== '') {
          etapasTemp[parseInt(etapaEditIndex)].nombre = etapaNombre;
          document.getElementById('etapaEditIndex').value = '';
          document.getElementById('btnEtapaTexto').textContent = 'Agregar';
        } else {
          etapasTemp.push({
            nombre: etapaNombre,
            orden: etapasTemp.length + 1
          });
        }

        document.getElementById('etapaNombre').value = '';
        document.getElementById('etapaCount').textContent = '0';
        document.getElementById('etapasBadge').textContent = etapasTemp.length;
        renderizarEtapas();
        validarFormularioCurso();
      }

      // Renderizar etapas
      function renderizarEtapas() {
        const container = document.getElementById('etapasListContainer');

        if (etapasTemp.length === 0) {
          container.innerHTML = '<p class="text-muted" id="noEtapasMessage">No hay etapas agregadas. Agrega al menos 3 etapas.</p>';
          return;
        }

        let html = '';
        etapasTemp.forEach((etapa, index) => {
          html += `
                  <div class="step-item">
                    <div class="step-number">${index + 1}</div>
                    <div class="step-content">
                      <div class="step-title">Etapa ${index + 1}</div>
                      <div class="step-description">${etapa.nombre}</div>
                    </div>
                    <div class="step-actions">
                      <button type="button" class="btn btn-sm btn-outline-warning" onclick="editarEtapa(${index})">
                        <i class="bi bi-pencil"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-outline-danger ml-2" onclick="eliminarEtapa(${index})">
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>
                  </div>
                `;
        });

        container.innerHTML = html;
      }

      // Editar etapa
      window.editarEtapa = function (index) {
        document.getElementById('etapaNombre').value = etapasTemp[index].nombre;
        document.getElementById('etapaEditIndex').value = index;
        document.getElementById('btnEtapaTexto').textContent = 'Actualizar';
        document.getElementById('etapaCount').textContent = etapasTemp[index].nombre.length;
      }

      // Eliminar etapa
      window.eliminarEtapa = function (index) {
        if (confirm('¿Eliminar esta etapa?')) {
          etapasTemp.splice(index, 1);
          etapasTemp.forEach((etapa, idx) => {
            etapa.orden = idx + 1;
          });
          document.getElementById('etapasBadge').textContent = etapasTemp.length;
          renderizarEtapas();
          validarFormularioCurso();
        }
      }

      // Validar formulario curso
      function validarFormularioCurso() {
        const cursoNombre = document.getElementById('cursoNombre').value.trim();
        const btnGuardar = document.getElementById('btnGuardarCurso');

        if (cursoNombre && etapasTemp.length >= 3) {
          btnGuardar.disabled = false;
        } else {
          btnGuardar.disabled = true;
        }
      }

      // Guardar curso
      window.guardarCurso = async function () {
        const cursoNombre = document.getElementById('cursoNombre').value.trim();
        const cursoDescripcion = document.getElementById('cursoDescripcion').value.trim();

        if (!cursoNombre || etapasTemp.length < 3) {
          alert('El curso debe tener un nombre y al menos 3 etapas');
          return;
        }

        // 🚨 VALIDACIÓN CROSS-CHECKING ANTES DE GUARDAR 🚨
        const existe = await validarCursoCrossChecking(cursoNombre);

        if (existe) {
          mostrarErrorCross(`El curso <strong>${cursoNombre}</strong> ya existe en el otro sistema.`);

          return; // ❌ NO PERMITIR CREAR
        }

        const curso = {
          nombre: cursoNombre,
          descripcion: cursoDescripcion,
          etapas: [...etapasTemp]
        };

        if (cursoEditandoIndex !== null) {
          cursos[cursoEditandoIndex] = curso;
          cursoEditandoIndex = null; // Limpiar después de editar
          // Ocultar mensaje de no cursos si estaba visible
          hideNoCourses();
        } else {
          cursos.push(curso);
          // Ocultar mensaje de no cursos cuando agregamos el primero
          hideNoCourses();
        }

        // Preparar vista de etapas antes de ir al paso 3
        document.getElementById('cursoEtapasTitulo').textContent = curso.nombre;

        let html = '';
        curso.etapas.forEach((etapa, idx) => {
          html += `
                  <div class="step-item">
                    <div class="step-number">${idx + 1}</div>
                    <div class="step-content">
                      <div class="step-title">Etapa ${idx + 1}</div>
                      <div class="step-description">${etapa.nombre}</div>
                    </div>
                  </div>
                `;
        });

        document.getElementById('etapasViewContainer').innerHTML = html;

        // Renderizar lista actualizada
        renderizarCursos();

        // IR AL PASO 3 (ver etapas del curso recién guardado)
        irAPaso(3);
      }

      // Renderizar lista de cursos
      function renderizarCursos() {
        const noCursosMessage = document.getElementById('noCursosMessage');
        const cursosGrid = document.getElementById('cursosGrid');
        const cursosCards = document.getElementById('cursosCards');

        if (cursos.length === 0) {
          noCursosMessage.style.display = 'block';
          cursosGrid.style.display = 'none';
          return;
        }

        noCursosMessage.style.display = 'none';
        cursosGrid.style.display = 'block';

        let html = '';
        cursos.forEach((curso, index) => {
          html += `
                  <div class="col-md-6">
                    <div class="curso-card">
                      <div class="curso-card-header">
                        <i class="bi bi-journal-text"></i> ${curso.nombre}
                      </div>
                      <div class="curso-card-body">
                        <span class="etapas-count">
                          <i class="bi bi-list-check"></i> ${curso.etapas.length} etapas
                        </span>
                        <div>
                          <button type="button" class="btn btn-sm btn-outline-info" onclick="verEtapasCurso(${index})">
                            <i class="bi bi-eye"></i>
                          </button>
                          <button type="button" class="btn btn-sm btn-outline-warning ml-1" onclick="editarCurso(${index})">
                            <i class="bi bi-pencil"></i>
                          </button>
                          <button type="button" class="btn btn-sm btn-outline-danger ml-1" onclick="eliminarCurso(${index})">
                            <i class="bi bi-trash"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                `;
        });

        cursosCards.innerHTML = html;
      }

      // Ver etapas de un curso
      window.verEtapasCurso = function (index) {
        const curso = cursos[index];
        document.getElementById('cursoEtapasTitulo').textContent = curso.nombre;

        let html = '';
        curso.etapas.forEach((etapa, idx) => {
          html += `
                  <div class="step-item">
                    <div class="step-number">${idx + 1}</div>
                    <div class="step-content">
                      <div class="step-title">Etapa ${idx + 1}</div>
                      <div class="step-description">${etapa.nombre}</div>
                    </div>
                  </div>
                `;
        });

        document.getElementById('etapasViewContainer').innerHTML = html;
        cursoEditandoIndex = index;
        irAPaso(3);
      }

      window.editarCurso = function (index) {
        cursoEditandoIndex = index;
        const curso = cursos[index];

        document.getElementById('cursoNombre').value = curso.nombre;
        document.getElementById('cursoDescripcion').value = curso.descripcion || '';
        document.getElementById('cursoNombreCount').textContent = curso.nombre.length;
        document.getElementById('cursoDescripcionCount').textContent = (curso.descripcion || '').length;

        etapasTemp = [...curso.etapas];
        document.getElementById('etapasBadge').textContent = etapasTemp.length;

        document.getElementById('btnGuardarTexto').textContent = 'Actualizar Curso';

        renderizarEtapas();
        validarFormularioCurso();
        irAPaso(2);
      }

      // Editar curso desde vista de etapas
      window.editarCursoDesdeEtapas = function () {
        // Si estamos viendo un curso específico (ya guardado en el array)
        if (cursoEditandoIndex !== null) {
          editarCurso(cursoEditandoIndex);
          return;
        }

        if (cursos.length > 0) {
          const ultimoIndex = cursos.length - 1;
          editarCurso(ultimoIndex);
        } else {
          mostrarErrorCross('No hay ningún curso para editar.');

        }
      }

      window.eliminarCurso = function (index) {
        if (confirm('¿Estás seguro de eliminar este curso y todas sus etapas?')) {
          cursos.splice(index, 1);
          renderizarCursos();
        }
      }

      $('#modalGestionarCursos').on('show.bs.modal', function () {
        renderizarCursos();
        irAPaso(1);
        capNombreSpan.textContent = nombreCapInput.value.trim() || 'Nueva Capacitación';
      });

      $('#modalGestionarCursos').on('hidden.bs.modal', function () {
        const alert = document.getElementById("alertErrorCross");
        if (alert) {
          alert.classList.add("d-none");
          alert.innerHTML = "";
        }
      });



      document.getElementById("formCapacitacion").addEventListener("submit", async function (e) {
        e.preventDefault(); // DETIENE ENVÍO AUTOMÁTICO

        // Validar que exista al menos 1 curso antes de continuar
        if (!Array.isArray(cursos) || cursos.length === 0) {
          showNoCourses();
          const msg = document.getElementById('noCoursesMessage');
          if (msg) msg.scrollIntoView({ behavior: 'smooth', block: 'center' });
          return;
        }

        hideNoCourses();

        const container = document.getElementById("cursosHiddenInputs");
        container.innerHTML = "";

        //VALIDACIÓN CROSS-CHECKING PARA TODOS LOS CURSOS
        for (const curso of cursos) {
          const existe = await validarCursoCrossChecking(curso.nombre);
          if (existe) {
            mostrarErrorCross(`El curso <strong>${curso.nombre}</strong> ya existe en el otro sistema.`);
            return; 
          }          
        }

        cursos.forEach((curso, i) => {
          addHiddenInput(container, `cursos[${i}][nombre]`, curso.nombre);
          if (curso.descripcion) {
            addHiddenInput(container, `cursos[${i}][descripcion]`, curso.descripcion);
          }
          curso.etapas.forEach((etapa, j) => {
            addHiddenInput(container, `cursos[${i}][etapas][${j}][nombre]`, etapa.nombre);
            addHiddenInput(container, `cursos[${i}][etapas][${j}][orden]`, etapa.orden);
          });
        });

        this.submit();
      });



      function addHiddenInput(container, name, value) {
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = name;
        input.value = value;
        container.appendChild(input);
      }

      document.getElementById("btnSubmitCapacitacion")
      .addEventListener("click", function () {
        if (!Array.isArray(cursos) || cursos.length === 0) {
          showNoCourses();
          document.getElementById('noCoursesMessage').scrollIntoView({behavior:'smooth', block:'center'});
          return;
        }
        hideNoCourses();
        document
          .getElementById("formCapacitacion")
          .dispatchEvent(new Event("submit", { cancelable: true, bubbles: true }));
      });



    });


  </script>



  <script src="/vendor/adminlte/plugins/jquery/jquery.min.js"></script>
  <script src="/vendor/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script>
    const INCENDIOS_URL = "{{ env('INCENDIOS_URL') }}";
  </script>

@endsection