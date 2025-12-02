@extends('adminlte::page')

@section('content')
<style>
  :root {
    --color-amarillo: #FFA726;
    --color-card: #ffffff;
    --color-texto-principal: #333333;
    --color-blanco: #f8f9fa;
    --color-azul: #007bff;
    --color-gris: #6c757d;
  }

  .form-container {
    justify-content: center;
    align-items: flex-start;
    padding: 40px 20px;
    width: 100%;
    min-height: 100vh;
  }

  .form-content {
    padding: 2rem 3rem;
    width: 100%;
    max-width: 1200px;
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
    color: var(--color-azul);
    font-weight: bold;
  }

  .listado-container {
    padding: 40px 20px;
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
    color: var(--color-azul);
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
    border: 1px solid var(--color-azul);
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
    color: var(--color-azul);
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
    border-color: var(--color-azul);
    box-shadow: 0 0 0 0.2rem rgba(255, 167, 38, 0.15);
  }

  .filtro-limpiar {
    display: flex;
    align-items: flex-end;
  }

  .filtro-limpiar button {
    padding: 10px 16px;
    border: none;
    background-color: var(--color-gris);
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
    color: var(--color-azul);
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
    border-left: 6px solid var(--color-azul);
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    text-decoration: none;
    color: inherit;
    position: relative;
  }

  .card-voluntario:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 20px rgba(0, 0, 0, 0.12);
    background-color: var(--color-blanco);
    text-decoration: none;
  }

  /* Indicador de nuevo voluntario */
  .indicador-nuevo {
    position: absolute;
    top: 15px;
    right: 15px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
    animation: pulseNuevo 2s infinite;
  }

  .circulo-nuevo {
    width: 16px;
    height: 16px;
    background-color: var(--color-azul);
    border-radius: 50%;
    box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.3);
  }

  .etiqueta-nuevo {
    background-color: var(--color-azul);
    color: white;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  @keyframes pulseNuevo {
    0%, 100% {
      opacity: 1;
    }
    50% {
      opacity: 0.6;
    }
  }

  .avatar {
    width: 48px;
    height: 48px;
    background-color: var(--color-azul);
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

  /* Mensaje de éxito */
  .alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 8px;
    animation: slideDown 0.5s ease;
  }

  .alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
  }

  .alert-dismissible .close {
    position: relative;
    top: -2px;
    right: -8px;
    padding: 0;
    background: transparent;
    border: 0;
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
    color: #000;
    opacity: .5;
    cursor: pointer;
  }

  .alert-dismissible .close:hover {
    opacity: .75;
  }

  @keyframes slideDown {
    from {
      opacity: 0;
      transform: translateY(-20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @media (max-width: 768px) {
    .listado-container {
      padding: 10px;
    }

    .form-titulo,
    .titulo-listado {
      font-size: 2rem;
    }

    .filtros-grid {
      grid-template-columns: 1fr;
    }

    .input-busqueda {
      max-width: 100%;
    }

    .indicador-nuevo {
      top: 10px;
      right: 10px;
    }

    .card-voluntario {
      padding: 14px 18px;
    }

    .avatar {
      width: 40px;
      height: 40px;
      font-size: 18px;
    }
  }
</style>

<div class="listado-container">
  <div class="listado-content">
    <header class="listado-header">
      <h1 class="form-titulo">Voluntarios</h1>
    </header>

    {{-- Mensaje de éxito --}}
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    @endif

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

              {{-- Indicador de nuevo voluntario --}}
              @if(session('nuevo_voluntario_id') == $voluntario->id_usuario)
                <div class="indicador-nuevo">
                  <div class="circulo-nuevo"></div>
                  <span class="etiqueta-nuevo">Nuevo</span>
                </div>
              @endif
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

  // Auto-ocultar el indicador de nuevo después de 10 segundos
  setTimeout(function() {
    const indicadores = document.querySelectorAll('.indicador-nuevo');
    indicadores.forEach(function(indicador) {
      indicador.style.transition = 'opacity 0.5s ease';
      indicador.style.opacity = '0';
      setTimeout(function() {
        indicador.remove();
      }, 500);
    });
  }, 10000);

  // Auto-ocultar alertas después de 5 segundos
  setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
      alert.style.transition = 'opacity 0.5s ease';
      alert.style.opacity = '0';
      setTimeout(function() {
        alert.remove();
      }, 500);
    });
  }, 5000);
</script>
@endsection