@extends('adminlte::page')
@section('title','Crear capacitación')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

@section('content')
<div class="card">
  <div class="card-header"><h3 class="card-title">Nueva capacitación</h3></div>

  <form action="{{ route('capacitaciones.store') }}" method="POST" id="formCapacitacion">
    @csrf

    {{-- aquí se inyectarán los inputs ocultos de cursos/etapas --}}
    <div id="cursosHiddenInputs"></div>

    <div class="card-body">
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
      <button type="submit" class="btn btn-primary">Guardar</button>

      <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalGestionarCursos">
          <i class="bi bi-list-ul"></i> Gestionar Cursos
      </button>
    </div>

  </form>
</div>

<!-- Modal Gestionar Cursos -->
<div class="modal fade" id="modalGestionarCursos" tabindex="-1" role="dialog" aria-labelledby="modalGestionarCursosLabel" aria-hidden="true">
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

        <!-- Nav Tabs -->
        <ul class="nav nav-tabs" id="cursosTabs" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="lista-tab" data-toggle="tab" href="#lista" role="tab">Lista de Cursos</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="formulario-tab" data-toggle="tab" href="#formulario" role="tab">
              <span id="tituloFormularioTab">Nuevo Curso</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="etapas-tab" data-toggle="tab" href="#etapas" role="tab">Etapas del Curso</a>
          </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content mt-3" id="cursosTabContent">

          <!-- TAB 1: Lista de Cursos -->
          <div class="tab-pane fade show active" id="lista" role="tabpanel">
            <div id="cursosListaContainer">
              <div class="text-center py-5" id="noCursosMessage">
                <p class="text-muted">No hay cursos agregados</p>
                <button type="button" class="btn btn-primary" onclick="mostrarFormularioNuevo()">
                  <i class="bi bi-plus-circle"></i> Agregar Primer Curso
                </button>
              </div>

              <div id="cursosGrid" style="display: none;">
                <h5 class="mb-3">Cursos</h5>
                <div class="row" id="cursosCards"></div>
                <div class="text-right mt-3">
                  <button type="button" class="btn btn-primary" onclick="mostrarFormularioNuevo()">
                    <i class="bi bi-plus-circle"></i> Agregar Nuevo Curso
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- TAB 2: Formulario Curso -->
          <div class="tab-pane fade" id="formulario" role="tabpanel">
            <div class="card card-outline card-primary">
              <div class="card-body">
                <input type="hidden" id="cursoEditIndex" value="">

                <div class="form-group">
                  <label for="cursoNombre">
                    Nombre del Curso
                    <span class="text-muted small">(<span id="cursoNombreCount">0</span>/100)</span>
                  </label>
                  <input type="text" class="form-control" id="cursoNombre" maxlength="100" placeholder="Nombre del curso">
                </div>

                <div class="form-group">
                  <label for="cursoDescripcion">
                    Descripción del Curso
                    <span class="text-muted small">(<span id="cursoDescripcionCount">0</span>/250)</span>
                  </label>
                  <textarea class="form-control" id="cursoDescripcion" rows="3" maxlength="250" placeholder="Descripción"></textarea>
                </div>

                <div class="form-group">
                  <label>Etapas del Curso (mínimo 3)</label>
                  <div class="input-group mb-3">
                    <input type="text" class="form-control" id="etapaNombre" maxlength="80" placeholder="Nombre de la etapa">
                    <input type="hidden" id="etapaEditIndex" value="">
                    <div class="input-group-append">
                      <button class="btn btn-primary" type="button" onclick="agregarEtapa()">
                        <span id="btnEtapaTexto">Agregar</span>
                      </button>
                    </div>
                  </div>

                  <small class="text-muted">
                    <span id="etapaCount">0</span>/80 caracteres
                  </small>

                  <div class="mt-3">
                    <h6>Etapas actuales:</h6>
                    <div id="etapasListContainer">
                      <p class="text-muted" id="noEtapasMessage">No hay etapas agregadas</p>
                    </div>
                  </div>
                </div>

                <div class="d-flex justify-content-between">
                  <button type="button" class="btn btn-secondary" onclick="volverALista()">Volver a la lista</button>

                  <button type="button" class="btn btn-primary" id="btnGuardarCurso" onclick="guardarCurso()"
                    disabled>
                    <span id="btnGuardarTexto">Agregar Curso</span>
                  </button>
                </div>

              </div>
            </div>
          </div>

          <!-- TAB 3: Ver Etapas -->
          <div class="tab-pane fade" id="etapas" role="tabpanel">
            <h4 class="text-primary mb-3" id="cursoEtapasTitulo">Curso</h4>
            <div id="etapasViewContainer">
              <p class="text-muted">No hay etapas para mostrar</p>
            </div>

            <div class="d-flex justify-content-between mt-4">
              <button type="button" class="btn btn-secondary" onclick="volverALista()">Volver a la lista</button>
              <div>
                <button type="button" class="btn btn-outline-primary mr-2" onclick="editarCursoDesdeEtapas()">Editar Curso</button>
                <button type="button" class="btn btn-primary" onclick="volverALista()">Finalizar</button>
              </div>
            </div>

          </div>
        </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
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
document.addEventListener('DOMContentLoaded', function () {
  let cursos = [];
  let etapasTemp = [];
  let cursoEditandoIndex = null;

  // actualizar título del modal con el nombre de la capacitación
  const nombreCapInput = document.getElementById('nombre');
  const capNombreSpan = document.getElementById('capacitacionNombre');
  if (nombreCapInput) {
    nombreCapInput.addEventListener('input', function () {
      capNombreSpan.textContent = this.value.trim() || 'Nueva Capacitación';
    });
  }

  // Contadores de caracteres
  document.getElementById('cursoNombre').addEventListener('input', function() {
    document.getElementById('cursoNombreCount').textContent = this.value.length;
    validarFormularioCurso();
  });

  document.getElementById('cursoDescripcion').addEventListener('input', function() {
    document.getElementById('cursoDescripcionCount').textContent = this.value.length;
  });

  document.getElementById('etapaNombre').addEventListener('input', function() {
    document.getElementById('etapaCount').textContent = this.value.length;
  });

  // Mostrar formulario nuevo
  window.mostrarFormularioNuevo = function () {
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

    document.getElementById('tituloFormularioTab').textContent = 'Nuevo Curso';
    document.getElementById('btnGuardarTexto').textContent = 'Agregar Curso';

    renderizarEtapas();
    $('#cursosTabs a[href="#formulario"]').tab('show');
  }

  // Agregar etapa
  window.agregarEtapa = function () {
    const etapaNombre = document.getElementById('etapaNombre').value.trim();
    const etapaEditIndex = document.getElementById('etapaEditIndex').value;

    if (!etapaNombre) return;

    if (etapaEditIndex !== '') {
      // Editar etapa existente
      etapasTemp[parseInt(etapaEditIndex)].nombre = etapaNombre;
      document.getElementById('etapaEditIndex').value = '';
      document.getElementById('btnEtapaTexto').textContent = 'Agregar';
    } else {
      // Agregar nueva etapa
      etapasTemp.push({
        nombre: etapaNombre,
        orden: etapasTemp.length + 1
      });
    }

    document.getElementById('etapaNombre').value = '';
    document.getElementById('etapaCount').textContent = '0';
    renderizarEtapas();
    validarFormularioCurso();
  }

  // Renderizar etapas
  function renderizarEtapas() {
    const container = document.getElementById('etapasListContainer');

    if (etapasTemp.length === 0) {
      container.innerHTML = '<p class="text-muted" id="noEtapasMessage">No hay etapas agregadas</p>';
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
              Editar
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger ml-2" onclick="eliminarEtapa(${index})">
              Eliminar
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
    etapasTemp.splice(index, 1);
    etapasTemp.forEach((etapa, idx) => {
      etapa.orden = idx + 1;
    });
    renderizarEtapas();
    validarFormularioCurso();
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

  // Guardar curso en array cursos[]
  window.guardarCurso = function () {
    const cursoNombre = document.getElementById('cursoNombre').value.trim();
    const cursoDescripcion = document.getElementById('cursoDescripcion').value.trim();

    if (!cursoNombre || etapasTemp.length < 3) return;

    const curso = {
      nombre: cursoNombre,
      descripcion: cursoDescripcion,
      etapas: [...etapasTemp]
    };

    if (cursoEditandoIndex !== null) {
      cursos[cursoEditandoIndex] = curso;
    } else {
      cursos.push(curso);
    }

    renderizarCursos();
    volverALista();
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
            <div class="curso-card-header">${curso.nombre}</div>
            <div class="curso-card-body">
              <span class="etapas-count">${curso.etapas.length} etapas</span>
              <div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="verEtapasCurso(${index})">
                  Ver etapas
                </button>
                <button type="button" class="btn btn-sm btn-outline-warning ml-1" onclick="editarCurso(${index})">
                  <i class="bi bi-pencil"></i> Editar
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger ml-1" onclick="eliminarCurso(${index})">
                  <i class="bi bi-trash"></i> Eliminar
                </button>
              </div>
            </div>
          </div>
        </div>
      `;
    });

    cursosCards.innerHTML = html;
  }

  // Ver etapas de un curso (tab Etapas)
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
    $('#cursosTabs a[href="#etapas"]').tab('show');
  }

  // Editar curso (lleva datos al formulario)
  window.editarCurso = function (index) {
    cursoEditandoIndex = index;
    const curso = cursos[index];

    document.getElementById('cursoNombre').value = curso.nombre;
    document.getElementById('cursoDescripcion').value = curso.descripcion || '';
    document.getElementById('cursoNombreCount').textContent = curso.nombre.length;
    document.getElementById('cursoDescripcionCount').textContent = (curso.descripcion || '').length;

    etapasTemp = [...curso.etapas];

    document.getElementById('tituloFormularioTab').textContent = 'Editar Curso';
    document.getElementById('btnGuardarTexto').textContent = 'Actualizar Curso';

    renderizarEtapas();
    validarFormularioCurso();
    $('#cursosTabs a[href="#formulario"]').tab('show');
  }

  // Editar curso desde tab de Etapas
  window.editarCursoDesdeEtapas = function () {
    if (cursoEditandoIndex !== null) {
      editarCurso(cursoEditandoIndex);
    }
  }

  // Eliminar curso
  window.eliminarCurso = function (index) {
    if (confirm('¿Estás seguro de eliminar este curso?')) {
      cursos.splice(index, 1);
      renderizarCursos();
    }
  }

  // Volver a lista de cursos (tab Lista)
  window.volverALista = function () {
    $('#cursosTabs a[href="#lista"]').tab('show');
  }

  // Al abrir modal, renderizar cursos actuales y sincronizar título
  $('#modalGestionarCursos').on('show.bs.modal', function () {
    renderizarCursos();
    capNombreSpan.textContent = nombreCapInput.value.trim() || 'Nueva Capacitación';
  });

  // Antes de enviar el form → generar inputs ocultos compatibles con tu validate()
  document.getElementById("formCapacitacion").addEventListener("submit", function () {
    const container = document.getElementById("cursosHiddenInputs");
    container.innerHTML = "";

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
  });

  function addHiddenInput(container, name, value) {
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = name;
    input.value = value;
    container.appendChild(input);
  }
});
</script>
@endsection
