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
                ->withHeaders([
                    'Content-Type' => 'application/json; charset=utf-8',
                    'Accept' => 'application/json'
                ])
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
                ->withHeaders([
                    'Content-Type' => 'application/json; charset=utf-8',
                    'Accept' => 'application/json'
                ])
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

    /**
     * Recomendar cursos usando Google Gemini AI
     * 
     * @param string $resumenFisico Resumen de la evaluación física del voluntario
     * @param string $resumenEmocional Resumen de la evaluación emocional
     * @param array $cursos Array de cursos disponibles con su información
     * @param string $nombreVoluntario Nombre del voluntario
     * @return array
     */
    public function recomendarCursos(string $resumenFisico, string $resumenEmocional, array $cursos, string $nombreVoluntario): array
    {
        try {
            // Usar API Key específica para cursos
            $apiKey = env('GOOGLE_GEMINI_API_KEY_CURSOS', 'AIzaSyB4dCvl25EaQTLgg9kPBNxih5s_uPqEmj8');
            
            // Preparar información de los cursos
            $cursosInfo = [];
            foreach ($cursos as $curso) {
                $cursosInfo[] = [
                    'id' => $curso['id'],
                    'nombre' => $curso['nombre'],
                    'descripcion' => $curso['descripcion'] ?? 'Sin descripción',
                    'capacitacion' => $curso['capacitacion_nombre'] ?? 'Sin capacitación'
                ];
            }

            // Crear el prompt para Gemini (OPTIMIZADO - MÚLTIPLES CURSOS)
            $prompt = "Analiza esta evaluación médica:\n\n";
            $prompt .= "FÍSICO: " . substr($resumenFisico, 0, 300) . "...\n";
            $prompt .= "EMOCIONAL: " . substr($resumenEmocional, 0, 300) . "...\n\n";
            $prompt .= "CURSOS:\n";
            
            foreach ($cursosInfo as $curso) {
                $prompt .= "ID:{$curso['id']} - {$curso['nombre']} ({$curso['capacitacion']})\n";
            }

            $prompt .= "\nRESPUESTA REQUERIDA:\n";
            $prompt .= "- Si hay problemas FÍSICOS y EMOCIONALES significativos Y existen cursos específicos para cada uno, recomienda 2 cursos (máximo):\n";
            $prompt .= "  CURSO_1:\n  NOMBRE: [nombre]\n  ID: [id]\n  TIPO: FÍSICO\n  RAZÓN: [breve]\n\n";
            $prompt .= "  CURSO_2:\n  NOMBRE: [nombre]\n  ID: [id]\n  TIPO: EMOCIONAL\n  RAZÓN: [breve]\n\n";
            $prompt .= "- Si solo hay un tipo de problema O un curso cubre ambos, recomienda solo 1:\n";
            $prompt .= "  CURSO_1:\n  NOMBRE: [nombre]\n  ID: [id]\n  TIPO: [FÍSICO/EMOCIONAL/AMBOS]\n  RAZÓN: [breve]\n\n";
            $prompt .= "- Si no hay cursos adecuados:\n  NO_RECOMENDACION";

            // Llamar a la API de Google Gemini
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 4096,
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Verificar si hay contenido en la respuesta
                if (!isset($data['candidates'][0]['content']['parts'])) {
                    Log::warning('Gemini - Respuesta sin parts', [
                        'finish_reason' => $data['candidates'][0]['finishReason'] ?? 'unknown',
                        'voluntario' => $nombreVoluntario
                    ]);
                    
                    return [
                        'success' => false,
                        'message' => 'La IA no pudo generar una respuesta completa. Razón: ' . ($data['candidates'][0]['finishReason'] ?? 'desconocida')
                    ];
                }
                
                $textoRespuesta = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

                Log::info('Gemini - Recomendación generada', [
                    'curso_mencionado' => substr($textoRespuesta, 0, 100),
                    'voluntario' => $nombreVoluntario
                ]);

                // Parsear la respuesta (SOPORTE MÚLTIPLES CURSOS)
                if (str_contains($textoRespuesta, 'NO_RECOMENDACION')) {
                    return [
                        'success' => true,
                        'tiene_recomendacion' => false,
                        'mensaje' => 'No hay cursos compatibles con los padecimientos actuales.'
                    ];
                }

                // Extraer múltiples cursos
                $cursos = [];
                
                // Buscar CURSO_1
                if (preg_match('/CURSO_1:.*?NOMBRE:\s*(.+?)[\n\r].*?ID:\s*(\d+).*?TIPO:\s*(.+?)[\n\r].*?RAZÓN:\s*(.+?)(?=CURSO_2:|$)/s', $textoRespuesta, $matches)) {
                    $cursos[] = [
                        'nombre' => trim($matches[1]),
                        'id' => (int)$matches[2],
                        'tipo' => trim($matches[3]),
                        'razon' => trim($matches[4])
                    ];
                }
                
                // Buscar CURSO_2 (opcional)
                if (preg_match('/CURSO_2:.*?NOMBRE:\s*(.+?)[\n\r].*?ID:\s*(\d+).*?TIPO:\s*(.+?)[\n\r].*?RAZÓN:\s*(.+?)$/s', $textoRespuesta, $matches)) {
                    $cursos[] = [
                        'nombre' => trim($matches[1]),
                        'id' => (int)$matches[2],
                        'tipo' => trim($matches[3]),
                        'razon' => trim($matches[4])
                    ];
                }

                if (count($cursos) > 0) {
                    return [
                        'success' => true,
                        'tiene_recomendacion' => true,
                        'cursos' => $cursos,
                        'total_cursos' => count($cursos),
                        'respuesta_completa' => $textoRespuesta
                    ];
                }

                // Si no se pudo parsear correctamente
                return [
                    'success' => true,
                    'tiene_recomendacion' => false,
                    'mensaje' => 'No se pudo procesar la recomendación de la IA.',
                    'respuesta_raw' => $textoRespuesta
                ];
            }

            Log::error('Gemini - Error en respuesta', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'Error al conectar con Google Gemini',
                'status' => $response->status()
            ];

        } catch (\Exception $e) {
            Log::error('Gemini - Excepción', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Error al procesar recomendación: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Evaluar aptitud del voluntario para asignarle necesidades
     * según su estado físico y emocional actual
     */
    public function evaluarAptitudNecesidades(
        string $resumenFisico,
        string $resumenEmocional,
        array $necesidadesDisponibles
    ): array {
        try {
            // Usar API Key específica para necesidades
            $apiKey = env('GOOGLE_GEMINI_API_KEY_NECESIDADES', 'AIzaSyA0MPmhWeTuO-sphHGogZaaRocHf2FduNg');
            
            if (empty($apiKey)) {
                return [
                    'success' => false,
                    'message' => 'API Key de Google Gemini para necesidades no configurada'
                ];
            }

            // Construir lista de necesidades
            $listaNecesidades = '';
            foreach ($necesidadesDisponibles as $nec) {
                $listaNecesidades .= "- ID: {$nec['id']}, TIPO: {$nec['tipo']}, DESCRIPCIÓN: " . 
                    (strlen($nec['descripcion'] ?? '') > 100 
                        ? substr($nec['descripcion'], 0, 100) . '...' 
                        : ($nec['descripcion'] ?? 'Sin descripción')) . "\n";
            }

            $prompt = <<<PROMPT
Eres un evaluador médico que determina la aptitud de voluntarios para atender necesidades humanitarias.

ESTADO DEL VOLUNTARIO:
Físico: {$resumenFisico}
Emocional: {$resumenEmocional}

NECESIDADES DISPONIBLES:
{$listaNecesidades}

CRITERIOS DE EVALUACIÓN:
1. APTO_TODAS: Si NO hay síntomas significativos, o los síntomas son mínimos/ausentes. El voluntario puede realizar todas las actividades sin limitaciones importantes.
2. APTO_ALGUNAS: Si hay síntomas MODERADOS que limitan actividades físicas MUY intensas o emocionalmente demandantes (rescates, emergencias extremas), pero puede realizar actividades regulares.
3. NO_APTO: Si hay múltiples síntomas SEVEROS o PERSISTENTES que impidan trabajar de forma segura. Incluye: dolor intenso frecuente, fatiga extrema, estrés severo constante, ansiedad incapacitante, o condiciones que requieran atención médica urgente.

IMPORTANTE: 
- Si el resumen indica "ninguna", "no presenta", "sin síntomas" → APTO_TODAS
- Si hay síntomas leves o ocasionales → APTO_TODAS (puede trabajar normalmente)
- Si hay síntomas moderados pero manejables → APTO_ALGUNAS
- Solo si hay síntomas severos o múltiples condiciones graves → NO_APTO

RESPONDE ESTRICTAMENTE EN ESTE FORMATO:

NIVEL: [APTO_TODAS | APTO_ALGUNAS | NO_APTO]
RAZON: [Máximo 80 caracteres, conciso y directo]
NECESIDADES_APTAS: [IDs separados por comas, o "NINGUNA" si NO_APTO, o "TODAS" si APTO_TODAS]

EJEMPLOS:
- NIVEL: APTO_TODAS
  RAZON: Sin síntomas significativos
  NECESIDADES_APTAS: TODAS

- NIVEL: APTO_ALGUNAS
  RAZON: Dolor moderado limita trabajo físico intenso
  NECESIDADES_APTAS: 2,5,9

- NIVEL: NO_APTO
  RAZON: Múltiples síntomas severos requieren descanso
  NECESIDADES_APTAS: NINGUNA
PROMPT;

            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.3,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 4096,
                    ]
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $textoRespuesta = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

                // Parsear respuesta
                preg_match('/NIVEL:\s*(APTO_TODAS|APTO_ALGUNAS|NO_APTO)/i', $textoRespuesta, $matchesNivel);
                preg_match('/RAZON:\s*(.+?)(?=NECESIDADES_APTAS:|$)/is', $textoRespuesta, $matchesRazon);
                preg_match('/NECESIDADES_APTAS:\s*(.+?)$/is', $textoRespuesta, $matchesNecesidades);

                $nivel = strtoupper($matchesNivel[1] ?? 'NO_APTO');
                $razon = trim($matchesRazon[1] ?? 'Sin evaluación');
                $necesidadesAptasTexto = trim($matchesNecesidades[1] ?? 'NINGUNA');

                // Procesar necesidades aptas
                $necesidadesAptas = [];
                if ($necesidadesAptasTexto === 'TODAS') {
                    $necesidadesAptas = array_column($necesidadesDisponibles, 'id');
                } elseif ($necesidadesAptasTexto !== 'NINGUNA') {
                    $idsTexto = preg_replace('/[^0-9,]/', '', $necesidadesAptasTexto);
                    if (!empty($idsTexto)) {
                        $necesidadesAptas = array_map('intval', explode(',', $idsTexto));
                    }
                }

                return [
                    'success' => true,
                    'nivel_aptitud' => $nivel,
                    'razon' => substr($razon, 0, 500), // Limitar longitud
                    'necesidades_aptas' => $necesidadesAptas,
                    'respuesta_raw' => $textoRespuesta
                ];
            }

            Log::error('Gemini Aptitud - Error en respuesta', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'Error al conectar con Google Gemini para evaluación de aptitud'
            ];

        } catch (\Exception $e) {
            Log::error('Gemini Aptitud - Excepción', [
                'message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Error al evaluar aptitud: ' . $e->getMessage()
            ];
        }
    }
}
