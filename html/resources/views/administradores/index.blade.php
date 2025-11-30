@extends('adminlte::page')

@section('title', 'Administradores')

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
            <h1 class="form-titulo">Administradores</h1>
            <div class="col-sm-6">
                <a href="{{ route('administradores.create') }}" class="btn btn-primary float-right">
                    <i class="fas fa-plus"></i> Agregar Administrador
                </a>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="card card-warning card-outline">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Buscar por nombre"
                                   id="filtroNombre">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <input type="text" class="form-control" placeholder="Buscar por CI" id="filtroCI">
                    </div>
                    <div class="col-md-3 mb-3">
                        <select class="form-control" id="filtroEstado">
                            <option value="">Todos los estados</option>
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <button class="btn btn-block btn-outline-primary" id="btnLimpiar">
                            <i class="fas fa-times"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mensaje flash --}}
        @if(session('success'))
            <div class="alert alert-success mt-3">
                {{ session('success') }}
            </div>
        @endif

        {{-- Cards de administradores --}}
        <div id="listaAdmins" class="mt-3">
            @forelse ($admins as $admin)
                <div class="card card-admin mb-3"
                     data-nombre="{{ strtolower($admin->nombres . ' ' . $admin->apellidos) }}"
                     data-ci="{{ $admin->ci }}"
                     data-estado="{{ strtolower($admin->estado) }}">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="avatar-admin">{{ $admin->iniciales }}</div>
                            </div>
                            <div class="col">
                                <div class="d-flex align-items-center mb-1">
                                    <h5 class="mb-0 mr-2">
                                        {{ $admin->nombres }} {{ $admin->apellidos }}
                                    </h5>
                                    <span class="badge badge-info badge-rol">
                                        {{ $admin->rol->nombre ?? 'Sin rol' }}
                                    </span>
                                </div>
                                <p class="mb-0 text-muted">
                                    <i class="fas fa-envelope mr-1"></i> {{ $admin->email }}
                                </p>
                                <p class="mb-0 text-muted small">
                                    <i class="fas fa-id-card mr-1"></i> CI: {{ $admin->ci }}
                                </p>
                            </div>
                            <div class="col-auto">
                                @if (strtolower($admin->estado) === 'activo')
                                    <span class="badge badge-success px-3 py-2">Activo</span>
                                    <form action="{{ route('administradores.toggle-estado', $admin->id_usuario) }}"
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-outline-danger ml-2">
                                            <i class="fas fa-ban"></i> Desactivar
                                        </button>
                                    </form>
                                @else
                                    <span class="badge badge-danger px-3 py-2">Inactivo</span>
                                    <form action="{{ route('administradores.toggle-estado', $admin->id_usuario) }}"
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-outline-success ml-2">
                                            <i class="fas fa-check"></i> Activar
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted">No hay administradores registrados todavía.</p>
            @endforelse
        </div>

    </div>
</div>
@endsection

@section('css')
<style>
  .card-admin { transition: transform 0.2s, box-shadow 0.2s; border-left: 4px solid #007bff; cursor: pointer; }
  .card-admin:hover { transform: translateY(-3px); box-shadow: 0 4px 15px rgba(0,0,0,0.15); }
  .avatar-admin { width: 50px; height: 50px; background: #007bff; color: white; font-weight: bold; font-size: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
  .badge-rol { font-size: 0.75rem; padding: 0.35em 0.65em; }
  .card-warning.card-outline { border-top: 3px solid #007bff !important; }
</style>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filtroNombre = document.getElementById('filtroNombre');
    const filtroCI     = document.getElementById('filtroCI');
    const filtroEstado = document.getElementById('filtroEstado');
    const btnLimpiar   = document.getElementById('btnLimpiar');
    const cards        = document.querySelectorAll('#listaAdmins .card-admin');

    function aplicarFiltros() {
        const nombre = filtroNombre.value.toLowerCase();
        const ci     = filtroCI.value.trim();
        const estado = filtroEstado.value.toLowerCase();

        cards.forEach(card => {
            const cNombre = card.dataset.nombre;
            const cCi     = card.dataset.ci;
            const cEstado = card.dataset.estado;

            const okNombre = !nombre || cNombre.includes(nombre);
            const okCi     = !ci || cCi.includes(ci);
            const okEstado = !estado || cEstado === estado;

            card.style.display = (okNombre && okCi && okEstado) ? '' : 'none';
        });
    }

    [filtroNombre, filtroCI, filtroEstado].forEach(input => {
        if (!input) return;
        const evt = input.tagName === 'SELECT' ? 'change' : 'input';
        input.addEventListener(evt, aplicarFiltros);
    });

    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function () {
            filtroNombre.value = '';
            filtroCI.value     = '';
            filtroEstado.value = '';
            aplicarFiltros();
        });
    }
});
</script>
@endsection
