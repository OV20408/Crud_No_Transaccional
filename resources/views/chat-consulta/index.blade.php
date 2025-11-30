@extends('adminlte::page')

@section('title', 'Chat de voluntarios')

@section('content_header')
    <h1>Chat consultas de voluntarios</h1>
@stop

@section('content')
@php
    $esEmergencia = $esEmergencia ?? false;
    $ayudaId = $ayudaId ?? null;
    $voluntarioSeleccionado = $voluntarioId ?? null;

    // Agrupamos por voluntario
    $conversaciones = $mensajes->groupBy('voluntario_id');

    // Lo transformamos a un JSON amigable para JS
    $conversacionesJson = $conversaciones->mapWithKeys(function ($items, $voluntarioId) {
        $primero = $items->first();
        $nombre  = trim(($primero->nombres ?? '') . ' ' . ($primero->apellidos ?? ''));
        $ci      = $primero->ci ?? '';

        $mensajesMap = $items->map(function ($m) {
            return [
                'id'    => $m->id,
                'tipo'  => $m->de === 'admin' ? 'admin' : 'voluntario',
                'texto' => $m->texto,
                'fecha' => $m->created_at
                    ? \Carbon\Carbon::parse($m->created_at)->format('d/m/Y H:i')
                    : null,
            ];
        });

        return [
            $voluntarioId => [
                'voluntario_id' => $voluntarioId,
                'nombre'        => $nombre,
                'ci'            => $ci,
                'mensajes'      => $mensajesMap,
            ],
        ];
    });
@endphp

<style>
    .chat-tabs {
        border-bottom: 2px solid #dee2e6;
        margin-bottom: 0;
    }
    .chat-tabs .nav-link {
        border: none;
        color: #6c757d;
        font-weight: 500;
        padding: 10px 20px;
        cursor: pointer;
        transition: all 0.3s;
    }
    .chat-tabs .nav-link:hover {
        color: #007bff;
        background-color: #f8f9fa;
    }
    .chat-tabs .nav-link.active {
        color: #007bff;
        border-bottom: 3px solid #007bff;
        background-color: transparent;
    }
    .badge-count {
        font-size: 0.75rem;
        padding: 2px 6px;
        border-radius: 10px;
        margin-left: 5px;
    }
</style>

<div class="row">

    {{-- LISTA DE VOLUNTARIOS (IZQUIERDA) --}}
    <div class="col-md-4">
        <div class="card card-outline card-primary h-100">
            <div class="card-header">
                {{-- TABS PARA FILTRAR --}}
                <ul class="nav nav-tabs chat-tabs" id="chat-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab-pendientes" href="#" data-estado="pendiente">
                            <i class="fas fa-clock text-danger"></i> 
                            Pendientes 
                            <span class="badge badge-danger badge-count" id="count-pendientes">0</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-respondidas" href="#" data-estado="respondido">
                            <i class="fas fa-check-circle text-success"></i> 
                            Respondidas 
                            <span class="badge badge-success badge-count" id="count-respondidas">0</span>
                        </a>
                    </li>
                </ul>

                {{-- Buscador --}}
                <div class="mt-3">
                    <input type="text"
                           id="buscador-voluntarios"
                           class="form-control form-control-sm"
                           placeholder="🔍 Buscar por nombre o CI">
                </div>
            </div>

            <div class="card-body p-0">
                <ul class="nav nav-pills flex-column" id="lista-voluntarios"
                    style="max-height: 500px; overflow-y: auto;">
                    {{-- Se llenará dinámicamente con JavaScript --}}
                </ul>
            </div>
        </div>
    </div>

    {{-- CHAT (DERECHA) --}}
    <div class="col-md-8">
        <div class="card card-primary direct-chat direct-chat-primary h-100">
            <div class="card-header">
                <h3 class="card-title" id="chat-titulo">
                    @if($esEmergencia)
                        <i class="fas fa-exclamation-triangle text-danger"></i> 
                    @endif
                    Selecciona un voluntario
                </h3>
            </div>

            <div class="card-body">
                <div class="direct-chat-messages" id="chat-mensajes"
                     style="height: 430px; overflow-y: auto;">
                    <p class="text-muted text-center mt-5">
                        No hay conversación seleccionada.
                    </p>
                </div>
            </div>

            <div class="card-footer">
                <form id="form-respuesta" method="POST" action="">
                    @csrf
                    <div class="input-group">
                        <input type="text"
                               name="respuesta_admin"
                               id="respuesta_admin"
                               class="form-control"
                               placeholder="Escribe una respuesta..."
                               autocomplete="off">
                        <span class="input-group-append">
                            <button type="submit"
                                    class="btn btn-primary"
                                    id="btn-enviar"
                                    disabled>
                                Enviar
                            </button>
                        </span>
                    </div>
                </form>

                {{-- Botones para emergencias --}}
                @if($esEmergencia && $ayudaId)
                    <div class="mt-2 d-flex gap-2" id="acciones-emergencia">
                        <button class="btn btn-success btn-sm" id="btn-marcar-resuelto">
                            <i class="fas fa-check-double"></i> Marcar como resuelta
                        </button>
                        
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
@stop

@section('js')
    @vite('resources/js/app.js')

    <script>
        window.CONVERSACIONES = @json($conversacionesJson);
        window.VOLUNTARIO_SELECCIONADO = {{ $voluntarioSeleccionado ?? 'null' }};
        window.ES_EMERGENCIA = {{ $esEmergencia ? 'true' : 'false' }};
        window.AYUDA_ID = {{ $ayudaId ?? 'null' }};
    </script>

    <script type="module">
        const conversaciones   = window.CONVERSACIONES || {};
        let voluntarioActual   = null;
        let estadoFiltro       = 'pendiente';

        const contenedorMensajes = document.getElementById('chat-mensajes');
        const tituloChat         = document.getElementById('chat-titulo');
        const formRespuesta      = document.getElementById('form-respuesta');
        const btnEnviar          = document.getElementById('btn-enviar');
        const inputRespuesta     = document.getElementById('respuesta_admin');
        const CHAT_API_URL       = '/api/chat-mensajes';

        // ============ FUNCIÓN PARA FILTRAR Y RENDERIZAR LISTA ============
        // ============ FUNCIÓN PARA FILTRAR Y RENDERIZAR LISTA ============
        function filtrarYRenderizarLista() {
            const listaVoluntarios = document.getElementById('lista-voluntarios');
            const buscadorValor = document.getElementById('buscador-voluntarios').value.toLowerCase();
            
            listaVoluntarios.innerHTML = '';

            let countPendientes = 0;
            let countRespondidas = 0;

            Object.entries(conversaciones).forEach(([volId, conv]) => {
                // ✅ PASO 1: Detectar emergencias activas
                const emergenciasActivas = [];
                const emergenciasResueltas = [];

                conv.mensajes.forEach(m => {
                    const matchEmergencia = m.texto.match(/🚨 \[EMERGENCIA #(\d+)\]/);
                    if (matchEmergencia) {
                        const emergenciaId = matchEmergencia[1];
                        if (!emergenciasActivas.includes(emergenciaId)) {
                            emergenciasActivas.push(emergenciaId);
                        }
                    }
                });

                conv.mensajes.forEach(m => {
                    if (m.texto.includes('✅ Tu emergencia ha sido resuelta')) {
                        const indexMensaje = conv.mensajes.indexOf(m);
                        for (let i = indexMensaje - 1; i >= 0; i--) {
                            const matchEmerg = conv.mensajes[i].texto.match(/🚨 \[EMERGENCIA #(\d+)\]/);
                            if (matchEmerg) {
                                const emergenciaId = matchEmerg[1];
                                if (!emergenciasResueltas.includes(emergenciaId)) {
                                    emergenciasResueltas.push(emergenciaId);
                                }
                                break;
                            }
                        }
                    }
                });

                const tieneEmergenciaActiva = emergenciasActivas.some(
                    id => !emergenciasResueltas.includes(id)
                );

                // ✅ PASO 2: Buscar último mensaje del admin
                let ultimoMensajeAdmin = null;
                for (let i = conv.mensajes.length - 1; i >= 0; i--) {
                    if (conv.mensajes[i].tipo === 'admin') {
                        ultimoMensajeAdmin = conv.mensajes[i];
                        break;
                    }
                }

                // ✅ PASO 3: Buscar mensajes del voluntario DESPUÉS del último admin
                let tieneMensajesNuevosVoluntario = false;
                if (ultimoMensajeAdmin) {
                    const indexUltimoAdmin = conv.mensajes.indexOf(ultimoMensajeAdmin);
                    for (let i = indexUltimoAdmin + 1; i < conv.mensajes.length; i++) {
                        if (conv.mensajes[i].tipo === 'voluntario') {
                            tieneMensajesNuevosVoluntario = true;
                            break;
                        }
                    }
                } else {
                    // Si no hay mensajes del admin, cualquier mensaje del voluntario es nuevo
                    tieneMensajesNuevosVoluntario = conv.mensajes.some(m => m.tipo === 'voluntario');
                }

                // ✅ PASO 4: Determinar estado final
                let estado;
                if (tieneEmergenciaActiva) {
                    // Caso 1: Hay emergencia sin resolver
                    estado = 'pendiente';
                } else if (tieneMensajesNuevosVoluntario) {
                    // Caso 2: Todas las emergencias resueltas PERO hay mensajes nuevos del voluntario
                    estado = 'pendiente';
                } else if (ultimoMensajeAdmin) {
                    // Caso 3: Admin respondió y voluntario no ha escrito nada nuevo
                    estado = 'respondido';
                } else {
                    // Caso 4: Conversación sin respuesta del admin
                    estado = 'pendiente';
                }

                // Contar
                if (estado === 'pendiente') countPendientes++;
                else countRespondidas++;

                // Filtrar por tab activo
                if (estadoFiltro !== estado) return;

                // Filtrar por búsqueda
                const nombreMatch = conv.nombre.toLowerCase().includes(buscadorValor);
                const ciMatch = (conv.ci || '').toLowerCase().includes(buscadorValor);
                if (buscadorValor && !nombreMatch && !ciMatch) return;

                // Crear elemento de lista
                const ultimoMensaje = conv.mensajes[conv.mensajes.length - 1];
                const preview = ultimoMensaje ? ultimoMensaje.texto.substring(0, 50) + '...' : 'Sin mensajes';
                const fecha = ultimoMensaje ? ultimoMensaje.fecha : '';

                const li = document.createElement('li');
                li.className = 'nav-item volunteer-item';
                li.dataset.voluntarioId = volId;
                li.dataset.nombre = conv.nombre;
                li.dataset.ci = conv.ci;

                const badgeColor = estado === 'pendiente' ? 'danger' : 'success';
                const badgeTexto = estado === 'pendiente' ? 'Pendiente' : 'Respondido';

                // Icono de emergencia activa
                const cantidadActivas = emergenciasActivas.length - emergenciasResueltas.length;
                const iconoEmergencia = tieneEmergenciaActiva 
                    ? `<i class="fas fa-exclamation-triangle text-danger mr-1" title="${cantidadActivas} emergencia(s) activa(s)"></i>` 
                    : '';

                // ✅ Icono de mensaje nuevo (si no hay emergencia activa pero hay mensajes pendientes)
                const iconoMensajeNuevo = (!tieneEmergenciaActiva && tieneMensajesNuevosVoluntario)
                    ? `<i class="fas fa-comment text-primary mr-1" title="Mensaje nuevo del voluntario"></i>`
                    : '';

                li.innerHTML = `
                    <a href="#" class="nav-link">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong class="nombre">${iconoEmergencia}${iconoMensajeNuevo}${conv.nombre}</strong><br>
                                <small class="text-muted">CI ${conv.ci}</small>
                            </div>
                            <div class="text-right">
                                <small class="text-muted d-block fecha-preview">${fecha}</small>
                                <span class="badge badge-${badgeColor}">${badgeTexto}</span>
                            </div>
                        </div>
                        <div>
                            <small class="text-muted d-block text-truncate preview-texto">
                                ${preview}
                            </small>
                        </div>
                    </a>
                `;

                li.addEventListener('click', e => {
                    e.preventDefault();
                    document.querySelectorAll('.volunteer-item .nav-link')
                        .forEach(a => a.classList.remove('active'));
                    li.querySelector('.nav-link').classList.add('active');
                    renderConversacion(volId);
                });

                listaVoluntarios.appendChild(li);
            });

            // Actualizar contadores
            document.getElementById('count-pendientes').textContent = countPendientes;
            document.getElementById('count-respondidas').textContent = countRespondidas;

            // Mensaje si no hay resultados
            if (listaVoluntarios.children.length === 0) {
                const mensaje = estadoFiltro === 'pendiente' 
                    ? 'No hay conversaciones pendientes' 
                    : 'No hay conversaciones respondidas';
                listaVoluntarios.innerHTML = `
                    <li class="nav-item p-3">
                        <span class="text-muted">${mensaje}.</span>
                    </li>
                `;
            }
        }

        // ============ LISTENERS PARA TABS ============
        document.getElementById('tab-pendientes').addEventListener('click', e => {
            e.preventDefault();
            estadoFiltro = 'pendiente';
            document.querySelectorAll('#chat-tabs .nav-link').forEach(tab => tab.classList.remove('active'));
            e.target.closest('.nav-link').classList.add('active');
            filtrarYRenderizarLista();
        });

        document.getElementById('tab-respondidas').addEventListener('click', e => {
            e.preventDefault();
            estadoFiltro = 'respondido';
            document.querySelectorAll('#chat-tabs .nav-link').forEach(tab => tab.classList.remove('active'));
            e.target.closest('.nav-link').classList.add('active');
            filtrarYRenderizarLista();
        });

        // ============ LISTENER PARA BUSCADOR ============
        document.getElementById('buscador-voluntarios').addEventListener('input', filtrarYRenderizarLista);

        // ============ RENDERIZAR CONVERSACIÓN ============
        function renderConversacion(voluntarioId) {
            voluntarioActual = voluntarioId;
            const conv = conversaciones[voluntarioId];

            contenedorMensajes.innerHTML = '';

            if (!conv) {
                tituloChat.innerText = 'Sin conversación';
                btnEnviar.disabled   = true;
                return;
            }

            const icono = window.ES_EMERGENCIA 
                ? '<i class="fas fa-exclamation-triangle text-danger"></i> '
                : '';
            tituloChat.innerHTML = `${icono}${conv.nombre} (CI ${conv.ci})`;
            btnEnviar.disabled   = false;

            conv.mensajes.forEach(m => {
                const wrapper = document.createElement('div');
                wrapper.classList.add('direct-chat-msg');
                if (m.tipo === 'admin') {
                    wrapper.classList.add('right');
                }

                wrapper.innerHTML = `
                    <div class="direct-chat-infos clearfix">
                        <span class="direct-chat-name ${m.tipo === 'admin' ? 'float-right' : 'float-left'}">
                            ${m.tipo === 'admin' ? 'Administrador' : conv.nombre}
                        </span>
                        <span class="direct-chat-timestamp ${m.tipo === 'admin' ? 'float-left' : 'float-right'}">
                            ${m.fecha ?? ''}
                        </span>
                    </div>
                    <div class="direct-chat-text">
                        ${m.texto ?? ''}
                    </div>
                `;

                contenedorMensajes.appendChild(wrapper);
            });

            contenedorMensajes.scrollTop = contenedorMensajes.scrollHeight;
        }

        // ============ BOTONES DE EMERGENCIA ============
        @if($esEmergencia && $ayudaId)
            const btnMarcarResuelto = document.getElementById('btn-marcar-resuelto');
            const btnVolver = document.getElementById('btn-volver');

            // ✅ Ocultar botón si la emergencia ya está resuelta
            const emergenciaActual = {{ $ayudaId }};
            const voluntarioActualId = {{ $voluntarioSeleccionado }};
            
            function verificarEstadoEmergencia() {
                const conv = conversaciones[voluntarioActualId];
                if (!conv) return;

                // Buscar si esta emergencia específica ya fue resuelta
                let emergenciaResuelta = false;
                
                conv.mensajes.forEach((m, index) => {
                    // Buscar mensaje de esta emergencia
                    if (m.texto.includes(`[EMERGENCIA #${emergenciaActual}]`)) {
                        // Verificar si hay un mensaje de resolución después
                        for (let i = index + 1; i < conv.mensajes.length; i++) {
                            if (conv.mensajes[i].texto.includes('✅ Tu emergencia ha sido resuelta')) {
                                emergenciaResuelta = true;
                                break;
                            }
                        }
                    }
                });

                // Ocultar o mostrar el botón
                if (btnMarcarResuelto) {
                    if (emergenciaResuelta) {
                        btnMarcarResuelto.style.display = 'none';
                    } else {
                        btnMarcarResuelto.style.display = 'inline-block';
                    }
                }
            }

            // Verificar al cargar
            verificarEstadoEmergencia();

            if (btnMarcarResuelto) {
                btnMarcarResuelto.addEventListener('click', async () => {
                    // ✅ SIN PROMPT - Confirmación simple
                    if (!confirm('¿Marcar esta emergencia como resuelta?\n\nSe notificará automáticamente al voluntario.')) {
                        return;
                    }

                    try {
                        // Deshabilitar botón mientras procesa
                        btnMarcarResuelto.disabled = true;
                        btnMarcarResuelto.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';

                        // 1. Actualizar estado de la solicitud
                        const resp = await fetch(`/api/solicitudes-ayuda/{{ $ayudaId }}/estado`, {
                            method: 'PATCH',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            },
                            body: JSON.stringify({ 
                                estado: 'respondido',
                                resolucion: 'Emergencia atendida y resuelta por el administrador' // ← Texto automático
                            }),
                        });

                        if (!resp.ok) throw new Error('Error al actualizar estado');

                        // 2. Enviar mensaje automático al chat
                        await fetch('/api/chat-mensajes', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                            },
                            body: JSON.stringify({
                                voluntario_id: {{ $voluntarioSeleccionado }},
                                de: 'admin',
                                texto: `✅ Tu emergencia ha sido resuelta. El equipo finalizó la atención de tu caso.`
                            }),
                        });

                        // 3. Mostrar notificación y recargar
                        alert('✅ Emergencia marcada como resuelta exitosamente');
                        
                        // ✅ Recargar página para actualizar todo
                        window.location.reload();

                    } catch (err) {
                        console.error('Error:', err);
                        alert('❌ Error al actualizar: ' + err.message);
                        
                        // Restaurar botón en caso de error
                        btnMarcarResuelto.disabled = false;
                        btnMarcarResuelto.innerHTML = '<i class="fas fa-check-double"></i> Marcar como resuelta';
                    }
                });
            }

            if (btnVolver) {
                btnVolver.addEventListener('click', () => {
                    window.location.href = '/ayudas_solicitadas';
                });
            }
        @endif

        // ============ WEBSOCKETS ============
        if (window.Echo) {
            console.log('✅ Echo disponible');

            const channel = window.Echo.channel('consultas');

            channel.listen('.MensajeChatCreado', ({ mensaje }) => {
                console.log('💬 Mensaje recibido:', mensaje);

                const volId = parseInt(mensaje.voluntario_id);

                if (!conversaciones[volId]) {
                    const nombre = mensaje.voluntario
                        ? `${mensaje.voluntario.nombres} ${mensaje.voluntario.apellidos}`
                        : `Voluntario ${volId}`;

                    conversaciones[volId] = {
                        voluntario_id: volId,
                        nombre,
                        ci: mensaje.voluntario ? mensaje.voluntario.ci : '',
                        mensajes: [],
                    };
                }

                const conv = conversaciones[volId];

                if (conv.mensajes.some(m => m.id === mensaje.id)) return;

                const fechaFormateada = mensaje.created_at 
                    ? new Date(mensaje.created_at).toLocaleString('es-BO')
                    : '';

                conv.mensajes.push({
                    id: mensaje.id,
                    tipo: mensaje.de === 'admin' ? 'admin' : 'voluntario',
                    texto: mensaje.texto,
                    fecha: fechaFormateada,
                });

                if (voluntarioActual == volId) {
                    renderConversacion(volId);
                }

                filtrarYRenderizarLista();
            });
        }

        // ============ ENVIAR MENSAJE ============
        formRespuesta.addEventListener('submit', async (e) => {
            e.preventDefault();

            const texto = inputRespuesta.value.trim();
            if (!texto || !voluntarioActual) return;

            btnEnviar.disabled = true;

            try {
                const resp = await fetch(CHAT_API_URL, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify({
                        voluntario_id: voluntarioActual,
                        de: 'admin',
                        texto,
                    }),
                });

                if (!resp.ok) throw new Error('Error HTTP ' + resp.status);

                inputRespuesta.value = '';
            } catch (err) {
                console.error('Error:', err);
                alert('Error al enviar');
            } finally {
                btnEnviar.disabled = false;
            }
        });

        // ============ INICIALIZACIÓN ============
        filtrarYRenderizarLista();

        if (window.VOLUNTARIO_SELECCIONADO) {
            setTimeout(() => {
                const itemVoluntario = document.querySelector(`[data-voluntario-id="${window.VOLUNTARIO_SELECCIONADO}"]`);
                if (itemVoluntario) {
                    itemVoluntario.querySelector('.nav-link').classList.add('active');
                    renderConversacion(window.VOLUNTARIO_SELECCIONADO);
                }
            }, 100);
        }
    </script>
@endsection