<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IAService
{
    protected string $baseUrl = 'http://18.218.3.153:5000';
    protected int $timeout = 30;

    /**
     * Generar evaluación emocional/psicológica
     */
    public function generarEmocion(array $respuestas): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/generar_emocion", [
                    'respuestas' => $respuestas
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            Log::error('IA Emoción - Error respuesta', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'Error al procesar evaluación emocional',
                'status' => $response->status()
            ];
        } catch (\Exception $e) {
            Log::error('IA Emoción - Excepción', [
                'message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'No se pudo conectar con el servicio de IA: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generar evaluación física
     */
    public function generarFisico(array $respuestas, array $cuerpo = []): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/generar_fisico", [
                    'respuestas' => $respuestas,
                    'cuerpo' => $cuerpo
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            Log::error('IA Físico - Error respuesta', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'Error al procesar evaluación física',
                'status' => $response->status()
            ];
        } catch (\Exception $e) {
            Log::error('IA Físico - Excepción', [
                'message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'No se pudo conectar con el servicio de IA: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generar evaluación completa (física + emocional)
     */
    public function generarEvaluacionCompleta(array $respuestasFisico, array $respuestasPsico, array $cuerpo = []): array
    {
        $resultadoFisico = $this->generarFisico($respuestasFisico, $cuerpo);
        $resultadoEmocion = $this->generarEmocion($respuestasPsico);

        return [
            'fisico' => $resultadoFisico,
            'emocional' => $resultadoEmocion,
            'success' => $resultadoFisico['success'] && $resultadoEmocion['success']
        ];
    }
}
