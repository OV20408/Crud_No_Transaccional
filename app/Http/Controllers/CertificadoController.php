<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class CertificadoController extends Controller
{
    /**
     * Generar certificado cuando el voluntario completa una capacitación
     */
    public function generarCertificado($idUsuario, $idCapacitacion)
    {
        try {
            // Verificar que todas las etapas estén completadas
            $etapasCompletadas = $this->verificarCapacitacionCompletada($idUsuario, $idCapacitacion);
            
            if (!$etapasCompletadas['completado']) {
                return response()->json([
                    'success' => false,
                    'message' => 'El voluntario aún no ha completado todas las etapas'
                ], 400);
            }

            // Verificar si ya existe certificado
            $certificadoExistente = DB::table('certificados')
                ->where('id_usuario', $idUsuario)
                ->where('id_capacitacion', $idCapacitacion)
                ->where('estado', 'activo')
                ->first();

            if ($certificadoExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe un certificado para esta capacitación'
                ], 400);
            }

            // Generar código único
            $codigoCertificado = $this->generarCodigoCertificado();

            // Obtener datos
            $voluntario = DB::table('usuario')->where('id_usuario', $idUsuario)->first();
            $capacitacion = DB::table('capacitacion')->where('id', $idCapacitacion)->first();
            $fechaFinalizacion = $etapasCompletadas['fecha_finalizacion'];
            $totalEtapas = $etapasCompletadas['total_etapas'];
            $totalHoras = $etapasCompletadas['total_horas'];

            // Generar PDF
            $pdf = Pdf::loadView('certificados.certificado-pdf', compact(
                'voluntario',
                'capacitacion',
                'codigoCertificado',
                'fechaFinalizacion',
                'totalEtapas',
                'totalHoras'
            ));

            $pdf->setPaper('a4', 'landscape');

            // Crear carpeta si no existe
            $carpetaCertificados = storage_path('app/public/certificados');
            if (!file_exists($carpetaCertificados)) {
                mkdir($carpetaCertificados, 0755, true);
            }

            // Guardar PDF
            $nombreArchivo = 'certificado_' . $idUsuario . '_' . $idCapacitacion . '_' . time() . '.pdf';
            $rutaPDF = 'certificados/' . $nombreArchivo;
            $pdf->save(storage_path('app/public/' . $rutaPDF));

            // Guardar en BD
            $certificadoId = DB::table('certificados')->insertGetId([
                'id_usuario' => $idUsuario,
                'id_capacitacion' => $idCapacitacion,
                'codigo_certificado' => $codigoCertificado,
                'fecha_emision' => now(),
                'archivo_pdf' => $rutaPDF,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Enviar email
            $this->enviarCertificadoPorEmail($voluntario, $capacitacion, $rutaPDF, $codigoCertificado);

            return response()->json([
                'success' => true,
                'message' => 'Certificado generado y enviado exitosamente',
                'certificado_id' => $certificadoId,
                'codigo' => $codigoCertificado
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar certificado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔥 Forzar regeneración de certificado (SOLO DESARROLLO)
     * Este método ELIMINA el certificado existente y lo regenera con el nuevo diseño
     */
    public function forzarRegeneracion($idUsuario, $idCapacitacion)
    {
        try {
            // 1. Eliminar certificado existente de la BD
            $certificadoAntiguo = DB::table('certificados')
                ->where('id_usuario', $idUsuario)
                ->where('id_capacitacion', $idCapacitacion)
                ->first();

            if ($certificadoAntiguo) {
                // Eliminar archivo PDF antiguo
                $rutaCompleta = storage_path('app/public/' . $certificadoAntiguo->archivo_pdf);
                if (file_exists($rutaCompleta)) {
                    unlink($rutaCompleta);
                }

                // Eliminar registro de BD
                DB::table('certificados')
                    ->where('id_usuario', $idUsuario)
                    ->where('id_capacitacion', $idCapacitacion)
                    ->delete();
            }

            // 2. Eliminar TODOS los PDFs antiguos de este voluntario/capacitación (por si acaso)
            $carpeta = storage_path('app/public/certificados');
            if (file_exists($carpeta)) {
                $archivos = glob($carpeta . '/certificado_' . $idUsuario . '_' . $idCapacitacion . '_*.pdf');
                foreach ($archivos as $archivo) {
                    if (file_exists($archivo)) {
                        unlink($archivo);
                    }
                }
            }

            // 3. Regenerar certificado con el nuevo diseño
            return $this->generarCertificado($idUsuario, $idCapacitacion);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al forzar regeneración: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Re-enviar certificado por email
     */
    public function reenviarCertificado($id)
    {
        try {
            $certificado = DB::table('certificados')->where('id', $id)->first();

            if (!$certificado || $certificado->estado !== 'activo') {
                return response()->json([
                    'success' => false,
                    'message' => 'Certificado no encontrado'
                ], 404);
            }

            // Obtener datos
            $voluntario = DB::table('usuario')->where('id_usuario', $certificado->id_usuario)->first();
            $capacitacion = DB::table('capacitacion')->where('id', $certificado->id_capacitacion)->first();

            if (!$voluntario->email) {
                return response()->json([
                    'success' => false,
                    'message' => 'El voluntario no tiene email registrado'
                ], 400);
            }

            // Re-enviar email
            $this->enviarCertificadoPorEmail($voluntario, $capacitacion, $certificado->archivo_pdf, $certificado->codigo_certificado);

            return response()->json([
                'success' => true,
                'message' => 'Certificado re-enviado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al re-enviar certificado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar si la capacitación está completada
     */
    private function verificarCapacitacionCompletada($idUsuario, $idCapacitacion)
    {
        $etapas = DB::table('progreso_voluntario')
            ->join('etapa', 'etapa.id', '=', 'progreso_voluntario.id_etapa')
            ->join('curso', 'curso.id', '=', 'etapa.id_curso')
            ->where('curso.id_capacitacion', $idCapacitacion)
            ->where('progreso_voluntario.id_usuario', $idUsuario)
            ->select(
                'progreso_voluntario.estado',
                'progreso_voluntario.fecha_finalizacion'
            )
            ->get();

        $totalEtapas = $etapas->count();
        $etapasCompletadas = $etapas->where('estado', 'completado')->count();
        $ultimaFechaFinalizacion = $etapas->max('fecha_finalizacion');

        return [
            'completado' => $totalEtapas > 0 && $totalEtapas === $etapasCompletadas,
            'total_etapas' => $totalEtapas,
            'total_horas' => $totalEtapas * 4, // Estimado
            'fecha_finalizacion' => $ultimaFechaFinalizacion
        ];
    }

    /**
     * Generar código único de certificado
     */
    private function generarCodigoCertificado()
    {
        $year = date('Y');
        $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        return 'CERT-' . $year . '-' . $random;
    }

    /**
     * Enviar certificado por email
     */
    private function enviarCertificadoPorEmail($voluntario, $capacitacion, $rutaPDF, $codigo)
    {
        if (!$voluntario->email) return;

        try {
            Mail::send('certificados.certificado_emitido', [
                'nombreVoluntario' => $voluntario->nombres . ' ' . $voluntario->apellidos,
                'nombreCapacitacion' => $capacitacion->nombre,
                'codigo' => $codigo
            ], function ($message) use ($voluntario, $rutaPDF) {
                $message->to($voluntario->email)
                        ->subject('¡Felicitaciones! Tu Certificado GEVOPI')
                        ->attach(storage_path('app/public/' . $rutaPDF));
            });

            // Marcar como enviado
            DB::table('certificados')
                ->where('codigo_certificado', $codigo)
                ->update([
                    'enviado_email' => true,
                    'fecha_envio_email' => now()
                ]);

        } catch (\Exception $e) {
            \Log::error('Error al enviar certificado: ' . $e->getMessage());
        }
    }

    /**
     * Descargar certificado (desde admin o móvil)
     */
    public function descargarCertificado($id)
    {
        $certificado = DB::table('certificados')->where('id', $id)->first();

        if (!$certificado || $certificado->estado !== 'activo') {
            abort(404, 'Certificado no encontrado');
        }

        $rutaCompleta = storage_path('app/public/' . $certificado->archivo_pdf);

        if (!file_exists($rutaCompleta)) {
            abort(404, 'Archivo no encontrado');
        }

        return response()->download($rutaCompleta);
    }

    /**
     * Listar certificados de un voluntario (para la móvil)
     */
    public function listarCertificados($idUsuario)
    {
        $certificados = DB::table('certificados')
            ->join('capacitacion', 'capacitacion.id', '=', 'certificados.id_capacitacion')
            ->where('certificados.id_usuario', $idUsuario)
            ->where('certificados.estado', 'activo')
            ->select(
                'certificados.*',
                'capacitacion.nombre as capacitacion_nombre'
            )
            ->orderBy('certificados.fecha_emision', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'certificados' => $certificados
        ]);
    }
}