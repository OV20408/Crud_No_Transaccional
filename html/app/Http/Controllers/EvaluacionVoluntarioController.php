<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Evaluacion;
use App\Models\Reporte;
use App\Models\Test;
use App\Mail\EvaluacionInvitacionMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EvaluacionVoluntarioController extends Controller
{
    /**
     * Enviar email de invitación al voluntario
     */
    public function enviarInvitacion(Request $request, $id)
    {
        try {
            $voluntario = User::where('id_usuario', $id)->firstOrFail();
            
            // Verificar que tenga email
            if (empty($voluntario->email)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El voluntario no tiene un email registrado.'
                ], 400);
            }
            
            // Generar token único
            $token = Str::random(64);
            
            // Guardar token en la base de datos
            DB::table('evaluacion_tokens')->insert([
                'id_voluntario' => $voluntario->id_usuario,
                'token' => $token,
                'usado' => false,
                'fecha_expiracion' => Carbon::now()->addDays(7),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            
            // Enviar email
            Mail::to($voluntario->email)->send(new EvaluacionInvitacionMail($voluntario, $token));
            
            return response()->json([
                'success' => true,
                'message' => 'Formulario enviado correctamente a ' . $voluntario->email
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el formulario: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mostrar la página de evaluación para el voluntario (vista restringida)
     */
    public function mostrarEvaluacion($token)
    {
        // Verificar token
        $tokenData = DB::table('evaluacion_tokens')
            ->where('token', $token)
            ->where('usado', false)
            ->where('fecha_expiracion', '>', Carbon::now())
            ->first();
            
        if (!$tokenData) {
            return view('evaluacion_voluntario.token_invalido');
        }
        
        $voluntario = User::where('id_usuario', $tokenData->id_voluntario)->first();
        
        if (!$voluntario) {
            return view('evaluacion_voluntario.token_invalido');
        }
        
        // Obtener tests disponibles
        $tests = Test::all();
        
        return view('evaluacion_voluntario.evaluacion', [
            'voluntario' => $voluntario,
            'token' => $token,
            'tests' => $tests
        ]);
    }
    
    /**
     * Procesar la evaluación del voluntario
     */
    public function procesarEvaluacion(Request $request, $token)
    {
        // Verificar token
        $tokenData = DB::table('evaluacion_tokens')
            ->where('token', $token)
            ->where('usado', false)
            ->where('fecha_expiracion', '>', Carbon::now())
            ->first();
            
        if (!$tokenData) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido o expirado'
            ], 400);
        }
        
        try {
            $voluntario = User::where('id_usuario', $tokenData->id_voluntario)->first();
            
            // Crear reporte
            $reporte = Reporte::create([
                'id_voluntario' => $voluntario->id_usuario,
                'resumen_fisico' => $request->input('resumen_fisico', ''),
                'resumen_emocional' => $request->input('resumen_emocional', ''),
                'estado_general' => $request->input('estado_general', 'Pendiente'),
                'observaciones' => $request->input('observaciones', ''),
                'fecha_generado' => Carbon::now()
            ]);
            
            // Guardar evaluaciones por cada test
            if ($request->has('respuestas')) {
                foreach ($request->respuestas as $testId => $respuestas) {
                    Evaluacion::create([
                        'id_voluntario' => $voluntario->id_usuario,
                        'id_test' => $testId,
                        'id_reporte' => $reporte->id,
                        'fecha' => Carbon::now(),
                        'puntaje' => $this->calcularPuntaje($respuestas)
                    ]);
                }
            }
            
            // Marcar token como usado
            DB::table('evaluacion_tokens')
                ->where('token', $token)
                ->update([
                    'usado' => true,
                    'updated_at' => Carbon::now()
                ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Evaluación completada correctamente',
                'reporte_id' => $reporte->id
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la evaluación: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Calcular puntaje de las respuestas
     */
    private function calcularPuntaje($respuestas)
    {
        if (!is_array($respuestas)) {
            return 0;
        }
        
        $puntaje = 0;
        foreach ($respuestas as $respuesta) {
            if (is_numeric($respuesta)) {
                $puntaje += (int) $respuesta;
            }
        }
        
        return $puntaje;
    }
    
    /**
     * Obtener historial de encuestas realizadas por un voluntario
     */
    public function historialEncuestas($idVoluntario)
    {
        $evaluaciones = DB::table('evaluaciones')
            ->join('tests', 'evaluaciones.id_test', '=', 'tests.id')
            ->where('evaluaciones.id_voluntario', $idVoluntario)
            ->select(
                'evaluaciones.*',
                'tests.nombre as test_nombre'
            )
            ->orderBy('evaluaciones.fecha', 'desc')
            ->get();
            
        return response()->json([
            'success' => true,
            'evaluaciones' => $evaluaciones
        ]);
    }
}
