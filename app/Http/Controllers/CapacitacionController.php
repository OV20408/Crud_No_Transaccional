<?php

namespace App\Http\Controllers;

use App\Models\Capacitacion;
use App\Models\Curso;
use App\Models\Etapa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CapacitacionController extends Controller
{
    public function __construct()
    {
        // ✅ CORREGIDO: Usar 'gestionar_capacitaciones' para todo
        $this->middleware('permission:gestionar_capacitaciones');
        
        // O si prefieres sin middleware (ya está protegido en routes/web.php):
        // Sin código aquí - las rutas ya tienen middleware(['role:Administrador'])
    }

    public function index(Request $request)
    {
        // Para los modales del index necesitamos cursos + etapas
        $capacitaciones = Capacitacion::with('cursos.etapas')->paginate(10);

        return view('capacitacion.index', compact('capacitaciones'))
            ->with('i', ($request->input('page', 1) - 1) * $capacitaciones->perPage());
    }

    public function create()
    {
        $capacitacion = new Capacitacion();
        return view('capacitacion.create', compact('capacitacion'));
    }

    public function edit($id)
    {
        $capacitacion = Capacitacion::with('cursos.etapas')->findOrFail($id);
        return view('capacitacion.edit', compact('capacitacion'));
    }

    /**
     * Normaliza el campo "cursos" si viene como JSON desde el formulario.
     */
    private function normalizarCursos(Request $request): void
    {
        $raw = $request->input('cursos');

        // Si viene como string JSON, lo convertimos a array PHP
        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $request->merge(['cursos' => $decoded]);
            } else {
                // Si el JSON está mal o viene raro, lo dejamos como array vacío
                $request->merge(['cursos' => []]);
            }
        }
        // Si no viene nada, simplemente no tocamos (o podrías forzar [])
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // 🔹 Convertir JSON de cursos a array antes de validar
            $this->normalizarCursos($request);

            $data = $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string|max:255',

                // cursos es opcional, pero si viene debe ser array
                'cursos' => 'required|array|min:1',
                'cursos.*.id' => 'nullable|integer',
                'cursos.*.nombre' => 'required|string|max:255',
                'cursos.*.descripcion' => 'nullable|string|max:255',
                'cursos.*.etapas' => 'required|array|min:3',
                'cursos.*.etapas.*.id' => 'nullable|integer',
                'cursos.*.etapas.*.nombre' => 'required|string|max:255',
                'cursos.*.etapas.*.orden' => 'required|integer',
            ]);

            $capacitacion = Capacitacion::create([
                'nombre'      => $data['nombre'],
                'descripcion' => $data['descripcion'] ?? null,
            ]);

            foreach ($data['cursos'] ?? [] as $cursoData) {
                $curso = Curso::create([
                    'nombre'         => $cursoData['nombre'],
                    'descripcion'    => $cursoData['descripcion'] ?? null,
                    'id_capacitacion'=> $capacitacion->id,
                ]);

                foreach ($cursoData['etapas'] as $etapaData) {
                    Etapa::create([
                        'nombre'   => $etapaData['nombre'],
                        'orden'    => $etapaData['orden'],
                        'id_curso' => $curso->id,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('capacitaciones.index')
                ->with('success', 'Capacitación creada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->withErrors('Error al crear la capacitación: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $capacitacion = Capacitacion::findOrFail($id);

            // 🔹 Igual: convertir JSON de cursos a array
            $this->normalizarCursos($request);

            $data = $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string|max:255',

                'cursos' => 'nullable|array',
                'cursos.*.id' => 'nullable|integer',
                'cursos.*.nombre' => 'required|string|max:255',
                'cursos.*.descripcion' => 'nullable|string|max:255',
                'cursos.*.etapas' => 'required|array|min:3',
                'cursos.*.etapas.*.id' => 'nullable|integer',
                'cursos.*.etapas.*.nombre' => 'required|string|max:255',
                'cursos.*.etapas.*.orden' => 'required|integer',
            ]);

            $capacitacion->update([
                'nombre'      => $data['nombre'],
                'descripcion' => $data['descripcion'] ?? null,
            ]);

            $cursosAnteriores = $capacitacion->cursos()->pluck('id')->toArray();
            $cursosActuales   = [];

            foreach ($data['cursos'] ?? [] as $cData) {

                if (!empty($cData['id'])) {
                    $curso = Curso::find($cData['id']);
                    $curso->update([
                        'nombre'      => $cData['nombre'],
                        'descripcion' => $cData['descripcion'] ?? null,
                    ]);
                } else {
                    $curso = Curso::create([
                        'nombre'         => $cData['nombre'],
                        'descripcion'    => $cData['descripcion'] ?? null,
                        'id_capacitacion'=> $capacitacion->id,
                    ]);
                }

                $cursosActuales[] = $curso->id;

                $etapasAnteriores = $curso->etapas()->pluck('id')->toArray();
                $etapasActuales   = [];

                foreach ($cData['etapas'] as $eData) {
                    if (!empty($eData['id'])) {
                        $etapa = Etapa::find($eData['id']);
                        $etapa->update([
                            'nombre' => $eData['nombre'],
                            'orden'  => $eData['orden'],
                        ]);
                    } else {
                        $etapa = Etapa::create([
                            'nombre'   => $eData['nombre'],
                            'orden'    => $eData['orden'],
                            'id_curso' => $curso->id,
                        ]);
                    }

                    $etapasActuales[] = $etapa->id;
                }

                $etapasParaEliminar = array_diff($etapasAnteriores, $etapasActuales);
                Etapa::destroy($etapasParaEliminar);
            }

            $cursosParaEliminar = array_diff($cursosAnteriores, $cursosActuales);
            Curso::destroy($cursosParaEliminar);

            DB::commit();

            return redirect()
                ->route('capacitaciones.index')
                ->with('success', 'Capacitación actualizada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->withErrors('Error al actualizar la capacitación: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $capacitacion = Capacitacion::with('cursos.etapas')->findOrFail($id);
        return view('capacitacion.show', compact('capacitacion'));
    }

    public function destroy($id)
    {
        $capacitacion = Capacitacion::findOrFail($id);
        $capacitacion->delete();

        return redirect()
            ->route('capacitaciones.index')
            ->with('success', 'Capacitación eliminada correctamente.');
    }
}
