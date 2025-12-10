@php
    /** @var \App\Models\Capacitacion|null $capacitacion */
    $cursosExistentes = isset($capacitacion)
        ? $capacitacion->cursos()->with('etapas')->get()
        : collect();

    $cursoIndexInicial = $cursosExistentes->count();
@endphp

<div class="modal fade" id="modalCursos" tabindex="-1" role="dialog"
     aria-labelledby="modalCursosLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalCursosLabel">
                    <i class="fas fa-layer-group"></i> Gestión de Cursos
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            {{-- BODY --}}
            <div class="modal-body">

                <div class="alert alert-info text-center">
                    Puedes agregar múltiples cursos y dentro de cada curso sus etapas correspondientes.
                </div>

                {{-- TABS --}}
                <ul class="nav nav-tabs" id="cursoTabs" role="tablist">
                    @foreach($cursosExistentes as $idx => $curso)
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $idx === 0 ? 'active' : '' }}"
                               id="curso-{{ $idx }}-tab"
                               data-toggle="tab"
                               href="#curso-{{ $idx }}"
                               role="tab">
                                Curso {{ $idx + 1 }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content p-3 border border-top-0 rounded-bottom" id="cursoTabsContent">

                    @foreach($cursosExistentes as $idx => $curso)
                        @php
                            $etapas = $curso->etapas->sortBy('orden')->values();
                        @endphp

                        <div class="tab-pane fade {{ $idx === 0 ? 'show active' : '' }}"
                             id="curso-{{ $idx }}" role="tabpanel">

                            <div class="card border-primary shadow-sm mb-3">
                                <div class="card-header bg-primary text-white">
                                    <i class="fas fa-book"></i> Datos del Curso
                                </div>

                                <div class="card-body">

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Nombre del Curso</label>
                                            <input type="text"
                                                   class="form-control"
                                                   name="cursos[{{ $idx }}][nombre]"
                                                   value="{{ old('cursos.'.$idx.'.nombre', $curso->nombre) }}"
                                                   required>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Descripción</label>
                                            <input type="text"
                                                   class="form-control"
                                                   name="cursos[{{ $idx }}][descripcion]"
                                                   value="{{ old('cursos.'.$idx.'.descripcion', $curso->descripcion) }}">
                                        </div>
                                    </div>

                                    <hr class="my-3">

                                    <label class="font-weight-bold mb-2">Etapas</label>

                                    <div id="etapas-{{ $idx }}">
                                        @foreach($etapas as $j => $etapa)
                                            <div class="card border-secondary p-3 mb-2 shadow-sm">
                                                <div class="row align-items-center">

                                                    <div class="col-md-5">
                                                        <label>Nombre de la etapa</label>
                                                        <input type="text"
                                                               name="cursos[{{ $idx }}][etapas][{{ $j }}][nombre]"
                                                               class="form-control"
                                                               value="{{ old('cursos.'.$idx.'.etapas.'.$j.'.nombre', $etapa->nombre) }}"
                                                               required>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label>Orden</label>
                                                        <input type="number"
                                                               name="cursos[{{ $idx }}][etapas][{{ $j }}][orden]"
                                                               class="form-control"
                                                               value="{{ old('cursos.'.$idx.'.etapas.'.$j.'.orden', $etapa->orden ?? $j + 1) }}"
                                                               min="1"
                                                               required>
                                                    </div>

                                                    <div class="col-md-2 text-right">
                                                        <button type="button"
                                                                class="btn btn-danger btn-sm mt-4"
                                                                onclick="this.closest('.card').remove();">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>

                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <button type="button"
                                            class="btn btn-outline-primary mt-2"
                                            onclick="addEtapa({{ $idx }})">
                                        <i class="fas fa-plus"></i> Agregar Etapa
                                    </button>

                                </div>
                            </div>

                        </div>
                    @endforeach

                </div>

                {{-- BOTÓN PARA AGREGAR NUEVO CURSO --}}
                <div class="text-right mt-3">
                    <button type="button" id="btnAddCurso" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Agregar Curso
                    </button>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                {{-- Este botón solo cierra el modal; el guardado real lo hace el submit del formulario principal --}}
                <button class="btn btn-success" data-dismiss="modal">
                    <i class="fas fa-save"></i> Listo
                </button>
            </div>

        </div>
    </div>
</div>

{{-- JS dinámico --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btnAddCurso = document.getElementById('btnAddCurso');
        if (!btnAddCurso) return;

        let cursoIndex = {{ $cursoIndexInicial }};

        btnAddCurso.addEventListener('click', function () {
            const tabId = 'curso-' + cursoIndex;
            const navTabs = document.getElementById('cursoTabs');
            const tabsContent = document.getElementById('cursoTabsContent');

            const isFirst = navTabs.children.length === 0;

            // TAB
            navTabs.insertAdjacentHTML('beforeend', `
                <li class="nav-item" role="presentation">
                    <a class="nav-link ${isFirst ? 'active' : ''}"
                       id="${tabId}-tab"
                       data-toggle="tab"
                       href="#${tabId}"
                       role="tab">
                        Curso ${cursoIndex + 1}
                    </a>
                </li>
            `);

            // CONTENIDO DEL TAB
            tabsContent.insertAdjacentHTML('beforeend', `
                <div class="tab-pane fade ${isFirst ? 'show active' : ''}"
                     id="${tabId}" role="tabpanel">

                    <div class="card border-primary shadow-sm mb-3">
                        <div class="card-header bg-primary text-white">
                            <i class="fas fa-book"></i> Datos del Curso
                        </div>

                        <div class="card-body">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre del Curso</label>
                                    <input type="text"
                                           class="form-control"
                                           name="cursos[${cursoIndex}][nombre]"
                                           required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Descripción</label>
                                    <input type="text"
                                           class="form-control"
                                           name="cursos[${cursoIndex}][descripcion]">
                                </div>
                            </div>

                            <hr class="my-3">

                            <label class="font-weight-bold mb-2">Etapas</label>

                            <div id="etapas-${cursoIndex}"></div>

                            <button type="button"
                                    class="btn btn-outline-primary mt-2"
                                    onclick="addEtapa(${cursoIndex})">
                                <i class="fas fa-plus"></i> Agregar Etapa
                            </button>

                        </div>
                    </div>

                </div>
            `);

            // Creamos 3 etapas por defecto
            addEtapa(cursoIndex);
            addEtapa(cursoIndex);
            addEtapa(cursoIndex);

            cursoIndex++;
        });

        // Si estamos en CREATE (no hay cursos), crear uno por defecto
        if (cursoIndex === 0) {
            btnAddCurso.click();
        }
    });

    function addEtapa(indexCurso) {
        const container = document.getElementById('etapas-' + indexCurso);
        if (!container) return;

        const etapaIndex = container.children.length;

        container.insertAdjacentHTML('beforeend', `
            <div class="card border-secondary p-3 mb-2 shadow-sm">
                <div class="row align-items-center">

                    <div class="col-md-5">
                        <label>Nombre de la etapa</label>
                        <input type="text"
                               name="cursos[${indexCurso}][etapas][${etapaIndex}][nombre]"
                               class="form-control"
                               required>
                    </div>

                    <div class="col-md-3">
                        <label>Orden</label>
                        <input type="number"
                               name="cursos[${indexCurso}][etapas][${etapaIndex}][orden]"
                               class="form-control"
                               value="${etapaIndex + 1}"
                               min="1"
                               required>
                    </div>

                    <div class="col-md-2 text-right">
                        <button type="button"
                                class="btn btn-danger btn-sm mt-4"
                                onclick="this.closest('.card').remove();">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>

                </div>
            </div>
        `);
    }
</script>
