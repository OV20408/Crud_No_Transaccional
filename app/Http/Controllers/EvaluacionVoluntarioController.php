<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Evaluacion;
use App\Models\Reporte;
use App\Models\Test;
use App\Models\HistorialClinico;
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
            
            // Obtener o crear historial clínico del voluntario
            $historial = HistorialClinico::firstOrCreate(
                ['id_usuario' => $voluntario->id_usuario],
                [
                    'fecha_inicio' => Carbon::now(),
                    'fecha_actualizacion' => Carbon::now()
                ]
            );
            
            // Usar los resúmenes enviados desde el frontend
            $resumenFisico = $request->input('resumen_fisico', 'Sin evaluación física');
            $resumenEmocional = $request->input('resumen_emocional', 'Sin evaluación emocional');
            $estadoGeneral = $request->input('estado_general', 'Completado');
            
            // Crear reporte
            $reporte = Reporte::create([
                'id_historial' => $historial->id,
                'resumen_fisico' => $resumenFisico,
                'resumen_emocional' => $resumenEmocional,
                'estado_general' => $estadoGeneral,
                'observaciones' => $request->input('observaciones', ''),
                'fecha_generado' => Carbon::now()
            ]);
            
            // Actualizar historial
            $historial->update(['fecha_actualizacion' => Carbon::now()]);
            
            // Crear evaluación (relacionada con el test de evaluación física/psicológica)
            // Buscar o usar el primer test disponible
            $test = Test::first();
            if ($test) {
                Evaluacion::create([
                    'id_reporte' => $reporte->id,
                    'id_test' => $test->id,
                    'id_universidad' => null,
                    'fecha' => Carbon::now()
                ]);
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
     * Obtener historial de encuestas realizadas por un voluntario
     */
    public function historialEncuestas($idVoluntario)
    {
        $historial = HistorialClinico::where('id_usuario', $idVoluntario)->first();
        
        if (!$historial) {
            return response()->json([
                'success' => true,
                'evaluaciones' => []
            ]);
        }
        
        $reportes = Reporte::where('id_historial', $historial->id)
            ->orderBy('fecha_generado', 'desc')
            ->get();
            
        return response()->json([
            'success' => true,
            'evaluaciones' => $reportes
        ]);
    }
}
