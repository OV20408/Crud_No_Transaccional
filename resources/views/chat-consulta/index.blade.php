@extends('adminlte::page')

@section('title', 'Chat de voluntarios')

@section('content_header')
    <h1>Chat consultas de voluntarios</h1>
@stop

@section('content')
@php
    // $mensajes viene de la ruta /chat-consulta
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

            <div class="card-body p-0">
                <ul class="nav nav-pills flex-column" id="lista-voluntarios"
                    style="max-height: 500px; overflow-y: auto;">

                    @forelse($conversaciones as $voluntarioId => $items)
                        @php
                            $ultimo  = $items->last();
                            $nombre  = trim(($ultimo->nombres ?? '') . ' ' . ($ultimo->apellidos ?? ''));
                            $ci      = $ultimo->ci ?? '';

                            // Estado "pendiente" si hay algún mensaje del voluntario sin leer (leido_en NULL)
                            $hayPendientes = $items->contains(function ($m) {
                                return $m->de === 'voluntario' && is_null($m->leido_en ?? null);
                            });
                            $estado  = $hayPendientes ? 'pendiente' : 'respondido';

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
                                        <small class="text-muted d-block">{{ $fecha }}</small>
                                        <span class="badge badge-{{ $estado === 'pendiente' ? 'danger' : 'success' }}">
                                            {{ ucfirst($estado) }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <small class="text-muted d-block text-truncate">
                                        {{ $preview }}
                                    </small>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="nav-item p-3">
                            <span class="text-muted">No hay conversaciones registradas.</span>
                        </li>
                    @endforelse

                </ul>
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
    </script>

    <script type="module">
        const conversaciones   = window.CONVERSACIONES || {};
        let voluntarioActual   = null;

        const contenedorMensajes = document.getElementById('chat-mensajes');
        const tituloChat         = document.getElementById('chat-titulo');
        const formRespuesta      = document.getElementById('form-respuesta');
        const btnEnviar          = document.getElementById('btn-enviar');
        const inputRespuesta     = document.getElementById('respuesta_admin');
        const CHAT_API_URL       = '/api/chat-mensajes';

        function renderConversacion(voluntarioId) {
            voluntarioActual = voluntarioId;
            const conv = conversaciones[voluntarioId];

            contenedorMensajes.innerHTML = '';

            if (!conv) {
                tituloChat.innerText = 'Sin conversación';
                btnEnviar.disabled   = true;
                return;
            }

            tituloChat.innerText = `${conv.nombre} (CI ${conv.ci})`;
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

        // Click en voluntarios (lista izquierda)
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

        // ============ WEBSOCKETS CON REVERB ============
        if (window.Echo) {
            console.log('✅ Echo está disponible, suscribiéndose al canal...');

            const channel = window.Echo.channel('consultas');

            channel.listen('MensajeChatCreado', ({ mensaje }) => {
                console.log('💬 MensajeChatCreado recibido:', mensaje);

                const volId = mensaje.voluntario_id;

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

                    // (Opcional) aquí podrías agregar dinámicamente el li de la izquierda
                }

                const conv = conversaciones[volId];

                // evitar duplicados
                if (conv.mensajes.some(m => m.id === mensaje.id)) {
                    return;
                }

                conv.mensajes.push({
                    id: mensaje.id,
                    tipo: mensaje.de === 'admin' ? 'admin' : 'voluntario',
                    texto: mensaje.texto,
                    fecha: mensaje.created_at,
                });

                if (voluntarioActual == volId) {
                    renderConversacion(volId);
                }
            });

            console.log('✅ Listener MensajeChatCreado configurado');
        } else {
            console.error('❌ Echo no está definido. Verifica bootstrap.js y que Vite esté corriendo.');
        }

        // Seleccionar la primera conversación por defecto
        const primer = document.querySelector('.volunteer-item');
        if (primer) {
        primer.querySelector('.nav-link').classList.add('active');
        renderConversacion(primer.dataset.voluntarioId);
        }

        // Interceptar submit del formulario para evitar refresh
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

                const json    = await resp.json();
                const mensaje = json.data;

                const conv = conversaciones[voluntarioActual];
                if (!conv.mensajes.some(m => m.id === mensaje.id)) {
                    conv.mensajes.push({
                        id: mensaje.id,
                        tipo: 'admin',
                        texto: mensaje.texto,
                        fecha: mensaje.created_at,
                    });
                }

                renderConversacion(voluntarioActual);
                inputRespuesta.value = '';
            } catch (err) {
                console.error(err);
                alert('Error al enviar la respuesta');
            } finally {
                btnEnviar.disabled = false;
            }
        });
    </script>
@endsection
