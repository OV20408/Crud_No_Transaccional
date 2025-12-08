{{-- Reemplazo completo de c:\Users\otera\crud\resources\views\chat-consulta\index.blade.php --}}
@extends('adminlte::page')

@section('title', 'Chat de voluntarios')

@section('content_header')
    <h1>Chat consultas de voluntarios</h1>
@stop

@section('content')
@php
    // $mensajes viene de la ruta /chat-consulta
    $esEmergencia = request()->query('emergencia') == '1';
    $voluntarioIdParam = request()->query('voluntario_id');
    $ayudaIdParam = request()->query('ayuda_id');
    
    // Agrupamos por voluntario
    $conversaciones = $mensajes->groupBy('voluntario_id');

    // Separar pendientes y respondidos
    $pendientes = collect();
    $respondidos = collect();

    foreach ($conversaciones as $voluntarioId => $items) {
    // Una conversación está "pendiente" si la última solicitud_ayuda está en "sin responder" o "en progreso"
    $ultimaSolicitud = DB::table('solicitudes_ayuda')
        ->where('voluntario_id', $voluntarioId)
        ->orderBy('created_at', 'desc')
        ->first();
    
    $hayPendientes = $ultimaSolicitud && 
                     in_array(strtolower($ultimaSolicitud->estado), ['sin responder', 'en progreso']);
    
    if ($hayPendientes) {
        $pendientes->put($voluntarioId, $items);
    } else {
        $respondidos->put($voluntarioId, $items);
    }
}

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

<div class="row">

    {{-- LISTA DE VOLUNTARIOS (IZQUIERDA) --}}
    <div class="col-md-4">
        <div class="card card-outline card-primary h-100">
            <div class="card-header">
                <h3 class="card-title">Voluntarios</h3>
                <div class="card-tools" style="width: 60%;">
                    <input type="text"
                           id="buscador-voluntarios"
                           class="form-control form-control-sm"
                           placeholder="Buscar por nombre o CI">
                </div>
            </div>

            {{-- PESTAÑAS --}}
            <div class="card-body p-0">
                <ul class="nav nav-tabs" id="tabsChats" role="tablist">
                    <li class="nav-item flex-fill">
                        <a class="nav-link active text-center" id="tab-pendientes" data-toggle="tab" href="#content-pendientes" role="tab">
                            Pendientes <span class="badge badge-danger" id="count-pendientes">{{ $pendientes->count() }}</span>
                        </a>
                    </li>
                    <li class="nav-item flex-fill">
                        <a class="nav-link text-center" id="tab-respondidos" data-toggle="tab" href="#content-respondidos" role="tab">
                            Respondidos <span class="badge badge-success" id="count-respondidos">{{ $respondidos->count() }}</span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    {{-- TAB PENDIENTES --}}
                    <div class="tab-pane fade show active" id="content-pendientes" role="tabpanel">
                        <ul class="nav nav-pills flex-column lista-conversaciones" data-tipo="pendientes"
                            style="max-height: 500px; overflow-y: auto;">
                            @forelse($pendientes as $voluntarioId => $items)
                                @php
                                    $ultimo  = $items->last();
                                    $nombre  = trim(($ultimo->nombres ?? '') . ' ' . ($ultimo->apellidos ?? ''));
                                    $ci      = $ultimo->ci ?? '';
                                    $fecha   = $ultimo->created_at
                                        ? \Carbon\Carbon::parse($ultimo->created_at)->format('d/m H:i')
                                        : '';
                                    $preview = \Illuminate\Support\Str::limit($ultimo->texto ?? '', 45);
                                @endphp

                                <li class="nav-item volunteer-item"
                                    data-voluntario-id="{{ $voluntarioId }}"
                                    data-nombre="{{ $nombre }}"
                                    data-ci="{{ $ci }}">
                                    <a href="#" class="nav-link">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <strong class="nombre">{{ $nombre }}</strong><br>
                                                <small class="text-muted">CI {{ $ci }}</small>
                                            </div>
                                            <div class="text-right">
                                                <small class="text-muted d-block fecha-preview">{{ $fecha }}</small>
                                                <span class="badge badge-danger">Pendiente</span>
                                            </div>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block text-truncate preview-texto">
                                                {{ $preview }}
                                            </small>
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li class="nav-item p-3">
                                    <span class="text-muted">No hay conversaciones pendientes.</span>
                                </li>
                            @endforelse
                        </ul>
                    </div>

                    {{-- TAB RESPONDIDOS --}}
                    <div class="tab-pane fade" id="content-respondidos" role="tabpanel">
                        <ul class="nav nav-pills flex-column lista-conversaciones" data-tipo="respondidos"
                            style="max-height: 500px; overflow-y: auto;">
                            @forelse($respondidos as $voluntarioId => $items)
                                @php
                                    $ultimo  = $items->last();
                                    $nombre  = trim(($ultimo->nombres ?? '') . ' ' . ($ultimo->apellidos ?? ''));
                                    $ci      = $ultimo->ci ?? '';
                                    $fecha   = $ultimo->created_at
                                        ? \Carbon\Carbon::parse($ultimo->created_at)->format('d/m H:i')
                                        : '';
                                    $preview = \Illuminate\Support\Str::limit($ultimo->texto ?? '', 45);
                                @endphp

                                <li class="nav-item volunteer-item"
                                    data-voluntario-id="{{ $voluntarioId }}"
                                    data-nombre="{{ $nombre }}"
                                    data-ci="{{ $ci }}">
                                    <a href="#" class="nav-link">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <strong class="nombre">{{ $nombre }}</strong><br>
                                                <small class="text-muted">CI {{ $ci }}</small>
                                            </div>
                                            <div class="text-right">
                                                <small class="text-muted d-block fecha-preview">{{ $fecha }}</small>
                                                <span class="badge badge-success">Respondido</span>
                                            </div>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block text-truncate preview-texto">
                                                {{ $preview }}
                                            </small>
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li class="nav-item p-3">
                                    <span class="text-muted">No hay conversaciones respondidas.</span>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CHAT (DERECHA) --}}
    <div class="col-md-8">
        <div class="card card-primary direct-chat direct-chat-primary h-100">
            <div class="card-header">
                <h3 class="card-title" id="chat-titulo">
                    Selecciona un voluntario
                </h3>
                @if($esEmergencia && $ayudaIdParam)
                    <div class="card-tools">
                        <button type="button" class="btn btn-success btn-sm" id="btn-marcar-resuelta" 
                                data-ayuda-id="{{ $ayudaIdParam }}" style="display: none;">
                            <i class="fas fa-check-circle"></i> Marcar como resuelta
                        </button>
                    </div>
                @endif
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
            </div>
        </div>
    </div>

</div>
@stop

@section('js')
    {{-- Cargamos el JS de Vite (donde está Echo/Reverb) --}}
    @vite('resources/js/app.js')

    {{-- Pasamos las conversaciones a JS ya estructuradas --}}
    <script>
        window.CONVERSACIONES = @json($conversacionesJson);
        window.ES_EMERGENCIA = {{ $esEmergencia ? 'true' : 'false' }};
        window.VOLUNTARIO_ID_PARAM = {{ $voluntarioIdParam ?? 'null' }};
        window.AYUDA_ID_PARAM = {{ $ayudaIdParam ?? 'null' }};
    </script>

    <script type="module">
        const conversaciones   = window.CONVERSACIONES || {};
        let voluntarioActual   = null;

        const contenedorMensajes = document.getElementById('chat-mensajes');
        const tituloChat         = document.getElementById('chat-titulo');
        const formRespuesta      = document.getElementById('form-respuesta');
        const btnEnviar          = document.getElementById('btn-enviar');
        const inputRespuesta     = document.getElementById('respuesta_admin');
        const btnMarcarResuelta  = document.getElementById('btn-marcar-resuelta');
        const CHAT_API_URL       = '/api/chat-mensajes';

        function renderConversacion(voluntarioId) {
            voluntarioActual = voluntarioId;
            const conv = conversaciones[voluntarioId];

            contenedorMensajes.innerHTML = '';

            if (!conv) {
                tituloChat.innerText = 'Sin conversación';
                btnEnviar.disabled   = true;
                if (btnMarcarResuelta) btnMarcarResuelta.style.display = 'none';
                return;
            }

            tituloChat.innerText = `${conv.nombre} (CI ${conv.ci})`;
            btnEnviar.disabled   = false;

            // Mostrar botón "Marcar como resuelta" si es emergencia
            if (btnMarcarResuelta && window.ES_EMERGENCIA && window.VOLUNTARIO_ID_PARAM == voluntarioId) {
                btnMarcarResuelta.style.display = 'inline-block';
            } else if (btnMarcarResuelta) {
                btnMarcarResuelta.style.display = 'none';
            }

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

        // Click en voluntarios (lista izquierda) - ambas pestañas
        document.querySelectorAll('.volunteer-item').forEach(item => {
            item.addEventListener('click', e => {
                e.preventDefault();

                document.querySelectorAll('.volunteer-item .nav-link')
                    .forEach(a => a.classList.remove('active'));

                item.querySelector('.nav-link').classList.add('active');

                renderConversacion(item.dataset.voluntarioId);
            });
        });

        // Buscador
        const buscador = document.getElementById('buscador-voluntarios');
        if (buscador) {
            buscador.addEventListener('input', e => {
                const term = e.target.value.toLowerCase();
                document.querySelectorAll('.volunteer-item').forEach(item => {
                    const nombre = item.dataset.nombre.toLowerCase();
                    const ci     = (item.dataset.ci || '').toLowerCase();
                    item.style.display =
                        (nombre.includes(term) || ci.includes(term)) ? '' : 'none';
                });
            });
        }

        // Botón "Marcar como resuelta"
        // Botón "Marcar como resuelta"
if (btnMarcarResuelta) {
    btnMarcarResuelta.addEventListener('click', async () => {
        const ayudaId = btnMarcarResuelta.dataset.ayudaId;
        
        btnMarcarResuelta.disabled = true;
        btnMarcarResuelta.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Marcando...';
        
        try {
            const resp = await fetch(`/api/solicitudes-ayuda/${ayudaId}/resolver`, {
                method: 'PATCH',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
            });

            if (!resp.ok) throw new Error('Error HTTP ' + resp.status);

            window.location.href = '/ayudas_solicitadas';
        } catch (err) {
            console.error('❌ Error:', err);
            btnMarcarResuelta.disabled = false;
            btnMarcarResuelta.innerHTML = '<i class="fas fa-check-circle"></i> Marcar como resuelta';
        }
    });
}

        // ============ WEBSOCKETS CON REVERB ============
        if (window.Echo) {
            console.log('✅ Echo está disponible, suscribiéndose al canal...');

            const channel = window.Echo.channel('consultas');

            channel.listen('.MensajeChatCreado', ({ mensaje }) => {
                console.log('💬 MensajeChatCreado recibido:', mensaje);

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

                    agregarVoluntarioALista(volId, nombre, mensaje.voluntario?.ci || '');
                }

                const conv = conversaciones[volId];

                if (conv.mensajes.some(m => m.id === mensaje.id)) {
                    console.log('⚠️ Mensaje duplicado, ignorando');
                    return;
                }

                const fechaFormateada = mensaje.created_at 
                    ? new Date(mensaje.created_at).toLocaleString('es-BO')
                    : '';

                conv.mensajes.push({
                    id: mensaje.id,
                    tipo: mensaje.de === 'admin' ? 'admin' : 'voluntario',
                    texto: mensaje.texto,
                    fecha: fechaFormateada,
                });

                console.log('✅ Mensaje agregado a conversación');

                if (voluntarioActual == volId) {
                    console.log('🔄 Re-renderizando conversación activa');
                    renderConversacion(volId);
                }

                actualizarPreviewVoluntario(volId, mensaje.texto, fechaFormateada);
            });

            channel.subscribed(() => {
                console.log('✅ Suscrito exitosamente al canal "consultas"');
            });

            channel.error((error) => {
                console.error('❌ Error en canal "consultas":', error);
            });

            console.log('✅ Listener .MensajeChatCreado configurado');
        } else {
            console.error('❌ Echo no está definido. Verifica bootstrap.js y que Vite esté corriendo.');
        }

        function agregarVoluntarioALista(volId, nombre, ci) {
            const listaPendientes = document.querySelector('[data-tipo="pendientes"]');
            const yaExiste = document.querySelector(`[data-voluntario-id="${volId}"]`);
            
            if (yaExiste) return;

            const li = document.createElement('li');
            li.className = 'nav-item volunteer-item';
            li.dataset.voluntarioId = volId;
            li.dataset.nombre = nombre;
            li.dataset.ci = ci;

            li.innerHTML = `
                <a href="#" class="nav-link">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong class="nombre">${nombre}</strong><br>
                            <small class="text-muted">CI ${ci}</small>
                        </div>
                        <div class="text-right">
                            <small class="text-muted d-block fecha-preview">Ahora</small>
                            <span class="badge badge-danger">Pendiente</span>
                        </div>
                    </div>
                    <div>
                        <small class="text-muted d-block text-truncate preview-texto">
                            Nuevo mensaje...
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

            if (listaPendientes) {
                listaPendientes.insertBefore(li, listaPendientes.firstChild);
                
                // Actualizar contador
                const countPendientes = document.getElementById('count-pendientes');
                if (countPendientes) {
                    const actual = parseInt(countPendientes.textContent) || 0;
                    countPendientes.textContent = actual + 1;
                }
            }
        }

        function actualizarPreviewVoluntario(volId, texto, fecha) {
            const item = document.querySelector(`[data-voluntario-id="${volId}"]`);
            if (!item) return;

            const previewTexto = item.querySelector('.preview-texto');
            const fechaPreview = item.querySelector('.fecha-preview');

            if (previewTexto) {
                previewTexto.textContent = texto.substring(0, 45) + (texto.length > 45 ? '...' : '');
            }

            if (fechaPreview) {
                fechaPreview.textContent = fecha;
            }

            const lista = item.closest('.lista-conversaciones');
            if (lista) {
                lista.insertBefore(item, lista.firstChild);
            }
        }

        // Seleccionar la primera conversación por defecto o la indicada por parámetro
        if (window.VOLUNTARIO_ID_PARAM) {
            const itemParam = document.querySelector(`[data-voluntario-id="${window.VOLUNTARIO_ID_PARAM}"]`);
            if (itemParam) {
                itemParam.querySelector('.nav-link').classList.add('active');
                renderConversacion(window.VOLUNTARIO_ID_PARAM);
                
                // Activar la pestaña correspondiente
                const badge = itemParam.querySelector('.badge');
                if (badge && badge.textContent.includes('Respondido')) {
                    document.getElementById('tab-respondidos').click();
                }
            }
        } else {
            const primer = document.querySelector('.volunteer-item');
            if (primer) {
                primer.querySelector('.nav-link').classList.add('active');
                renderConversacion(primer.dataset.voluntarioId);
            }
        }

        // Interceptar submit del formulario
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

                if (!resp.ok) {
                    throw new Error('Error HTTP ' + resp.status);
                }

                const json = await resp.json();
                console.log('✅ Mensaje enviado:', json);

                inputRespuesta.value = '';
            } catch (err) {
                console.error('❌ Error al enviar:', err);
                alert('Error al enviar la respuesta');
            } finally {
                btnEnviar.disabled = false;
            }
        });
    </script>
@endsection