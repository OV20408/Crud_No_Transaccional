<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IAService
{
    protected string $baseUrl = 'http://18.218.3.153:5000';
    protected int $timeout = 60;

    /**
     * Generar evaluación emocional/psicológica
     * La IA espera: {"evaluacion": "texto descriptivo de la evaluación"}
     */
    public function generarEmocion(string $evaluacion): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/generar_emocion", [
                    'evaluacion' => $evaluacion
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data,
                    'respuesta' => $data['respuesta'] ?? $data
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
     * La IA espera: {"evaluacion": "texto descriptivo de la evaluación física"}
     */
    public function generarFisico(string $evaluacion): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/generar_fisico", [
                    'evaluacion' => $evaluacion
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data,
                    'respuesta' => $data['respuesta'] ?? $data
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
    public function generarEvaluacionCompleta(string $evaluacionFisica, string $evaluacionEmocional): array
    {
        $resultadoFisico = $this->generarFisico($evaluacionFisica);
        $resultadoEmocion = $this->generarEmocion($evaluacionEmocional);

        return [
            'fisico' => $resultadoFisico,
            'emocional' => $resultadoEmocion,
            'success' => $resultadoFisico['success'] && $resultadoEmocion['success']
        ];
    }
}
