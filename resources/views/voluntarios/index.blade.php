@extends('adminlte::page')

@section('content')
<style>
  :root {
    --color-amarillo: #FFA726;
    --color-card: #ffffff;
    --color-texto-principal: #333333;
    --color-blanco: #f8f9fa;
  }

  .listado-container {
    padding: 20px;
    width: 100%;
    box-sizing: border-box;
    min-height: 100vh;
  }

  .listado-content {
    width: 100%;
    box-sizing: border-box;
  }

  .listado-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }

  .titulo-listado {
    color: var(--color-amarillo);
    font-size: 2.5rem;
    margin-bottom: 20px;
    font-weight: bold;
  }

  .listado-paneles {
    display: flex;
    flex-direction: column;
    gap: 30px;
  }

  .panel-barrabusqueda {
    border-radius: 12px;
    padding: 25px;
    background: var(--color-card);
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
  }

  .barra-busqueda {
    width: 100%;
    margin-bottom: 20px;
  }

  .input-busqueda {
    padding: 12px 18px;
    font-size: 16px;
    border-radius: 25px;
    border: 1px solid var(--color-amarillo);
    width: 100%;
    max-width: 85%;
    transition: border-color 0.3s ease;
  }

  .input-busqueda:focus {
    outline: none;
    border-color: var(--color-amarillo);
    box-shadow: 0 0 0 0.2rem rgba(255, 167, 38, 0.25);
  }

  .filtros-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 20px;
  }

  .filtros-grid label {
    font-weight: 600;
    color: var(--color-amarillo);
    display: block;
    margin-bottom: 5px;
  }

  .filtros-grid input,
  .filtros-grid select {
    width: 100%;
    padding: 10px 14px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 14px;
    transition: border-color 0.3s ease;
  }

  .filtros-grid input:focus,
  .filtros-grid select:focus {
    outline: none;
    border-color: var(--color-amarillo);
    box-shadow: 0 0 0 0.2rem rgba(255, 167, 38, 0.15);
  }

  .filtro-limpiar {
    display: flex;
    align-items: flex-end;
  }

  .filtro-limpiar button {
    padding: 10px 16px;
    border: none;
    background-color: #c62828;
    color: white;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
    width: 100%;
  }

  .filtro-limpiar button:hover {
    background-color: #a00000;
  }

  .panel-listadovol {
    background: transparent;
  }

  .lista {
    display: flex;
    flex-direction: column;
    gap: 20px;
  }

  .mensaje-vacio {
    color: var(--color-amarillo);
    font-style: italic;
    padding: 20px;
    text-align: center;
    background: var(--color-card);
    border-radius: 12px;
  }

  /* Card de Voluntario */
  .card-voluntario {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 18px 24px;
    background-color: var(--color-card);
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    border-left: 6px solid var(--color-amarillo);
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    text-decoration: none;
    color: inherit;
  }

  .card-voluntario:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 20px rgba(0, 0, 0, 0.12);
    background-color: var(--color-blanco);
    text-decoration: none;
  }

  .avatar {
    width: 48px;
    height: 48px;
    background-color: var(--color-amarillo);
    color: white;
    font-weight: bold;
    font-size: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .info-voluntario {
    display: flex;
    flex-direction: column;
    flex: 1;
  }

  .nombre-estado {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 6px;
  }

  .nombre-estado h4 {
    margin: 0;
    font-size: 18px;
    color: var(--color-texto-principal);
    font-weight: 600;
  }

  .estado {
    font-size: 13px;
    padding: 4px 12px;
    border-radius: 20px;
    font-weight: 600;
  }

  .estado.activo {
    background-color: rgba(46, 125, 50, 0.1);
    color: #2e7d32;
  }

  .estado.inactivo {
    background-color: rgba(198, 40, 40, 0.1);
    color: #c62828;
  }

  .info-voluntario p {
    margin: 0;
    font-size: 14px;
    color: #666;
  }

  @media (max-width: 768px) {
    .listado-container {
      padding: 10px;
    }

    .titulo-listado {
      font-size: 2rem;
    }

    .filtros-grid {
      grid-template-columns: 1fr;
    }

    .input-busqueda {
      max-width: 100%;
    }
  }
</style>

<div class="listado-container">
  <div class="listado-content">
    <header class="listado-header">
      <h1 class="titulo-listado">Voluntarios</h1>
    </header>

    <section class="listado-paneles">
      {{-- Panel de búsqueda y filtros --}}
      <div class="panel-barrabusqueda">
        <form action="{{ route('voluntarios.index') }}" method="GET" id="filtrosForm">
          <div class="barra-busqueda">
            <input
              type="search"
              name="q"
              class="input-busqueda"
              placeholder="Buscar por nombre"
              value="{{ request('q') }}"
            />
          </div>

          <div class="filtros-grid">
            <div>
              <label>CI</label>
              <input
                type="text"
                name="ci"
                placeholder="Buscar por CI"
                value="{{ request('ci') }}"
              />
            </div>

            <div>
              <label>Tipo de Sangre</label>
              <select name="tipo_sangre">
                <option value="">Todos</option>
                <option value="O+" {{ request('tipo_sangre') === 'O+' ? 'selected' : '' }}>O+</option>
                <option value="O-" {{ request('tipo_sangre') === 'O-' ? 'selected' : '' }}>O-</option>
                <option value="A+" {{ request('tipo_sangre') === 'A+' ? 'selected' : '' }}>A+</option>
                <option value="A-" {{ request('tipo_sangre') === 'A-' ? 'selected' : '' }}>A-</option>
                <option value="B+" {{ request('tipo_sangre') === 'B+' ? 'selected' : '' }}>B+</option>
                <option value="B-" {{ request('tipo_sangre') === 'B-' ? 'selected' : '' }}>B-</option>
                <option value="AB+" {{ request('tipo_sangre') === 'AB+' ? 'selected' : '' }}>AB+</option>
                <option value="AB-" {{ request('tipo_sangre') === 'AB-' ? 'selected' : '' }}>AB-</option>
              </select>
            </div>

            <div>
              <label>Disponibilidad</label>
              <select name="estado">
                <option value="">Todos</option>
                <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activo</option>
                <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
              </select>
            </div>

            <div class="filtro-limpiar">
              <button type="button" onclick="limpiarFiltros()">
                <i class="fas fa-times"></i> Limpiar filtros
              </button>
            </div>
          </div>
        </form>
      </div>

      {{-- Panel de lista de voluntarios --}}
      <div class="panel-listadovol">
        <div class="lista">
          @forelse($voluntarios as $voluntario)
            <a href="{{ route('voluntarios.show', $voluntario->id_usuario) }}" class="card-voluntario">
              <div class="avatar">
                <span>{{ strtoupper(substr($voluntario->nombres, 0, 1)) }}</span>
              </div>
              <div class="info-voluntario">
                <div class="nombre-estado">
                  <h4>{{ $voluntario->nombres }} {{ $voluntario->apellidos }}</h4>
                  <span class="estado {{ strtolower($voluntario->estado) }}">
                    {{ ucfirst($voluntario->estado) }}
                  </span>
                </div>
                <p>CI: {{ $voluntario->ci }} &nbsp; | &nbsp; Tipo de Sangre: {{ $voluntario->tipo_sangre ?? 'N/D' }}</p>
              </div>
            </a>
          @empty
            <p class="mensaje-vacio">No se encontraron voluntarios.</p>
          @endforelse
        </div>
      </div>
    </section>
  </div>
</div>

<script>
  // Auto-submit al cambiar filtros
  document.querySelectorAll('#filtrosForm select, #filtrosForm input').forEach(element => {
    element.addEventListener('change', function() {
      document.getElementById('filtrosForm').submit();
    });
  });

  // Limpiar filtros
  function limpiarFiltros() {
    document.querySelectorAll('#filtrosForm input[type="text"], #filtrosForm input[type="search"]').forEach(input => {
      input.value = '';
    });
    document.querySelectorAll('#filtrosForm select').forEach(select => {
      select.value = '';
    });
    document.getElementById('filtrosForm').submit();
  }
</script>
@endsection