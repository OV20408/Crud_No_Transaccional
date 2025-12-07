<?php

/**
 * Script de prueba para verificar la conexión con Google Gemini 1.5 Flash
 * 
 * Uso: php test_gemini.php
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Http;

// Cargar variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Obtener la API Key desde .env
$apiKey = $_ENV['GOOGLE_GEMINI_API_KEY_CURSOS'] ?? null;

if (!$apiKey) {
    die("❌ Error: No se encontró GOOGLE_GEMINI_API_KEY_CURSOS en el archivo .env\n");
}

echo "🔍 Probando conexión con Google Gemini 2.5 Flash...\n\n";

// Prompt de prueba
$prompt = "Hola, este es un mensaje de prueba. Por favor responde con 'Conexión exitosa con Gemini 2.5 Flash' y añade un dato curioso sobre inteligencia artificial.";

try {
    // Realizar petición a la API de Gemini
    $response = file_get_contents(
        "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}",
        false,
        stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => json_encode([
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
                        'maxOutputTokens' => 1024,
                    ]
                ]),
                'timeout' => 30
            ]
        ])
    );

    $data = json_decode($response, true);

    if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
        $respuesta = $data['candidates'][0]['content']['parts'][0]['text'];
        
        echo "✅ Conexión exitosa!\n\n";
        echo "📝 Respuesta de Gemini 2.5 Flash:\n";
        echo "─────────────────────────────────────────────\n";
        echo $respuesta . "\n";
        echo "─────────────────────────────────────────────\n\n";
        
        // Mostrar información adicional
        echo "ℹ️  Información de la respuesta:\n";
        echo "   - Modelo: gemini-2.5-flash\n";
        echo "   - Tokens usados: " . ($data['usageMetadata']['totalTokenCount'] ?? 'N/A') . "\n";
        echo "   - Tiempo de respuesta: Exitoso\n\n";
        
    } else {
        echo "❌ Error: Respuesta inesperada de la API\n";
        echo "Respuesta completa: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
    }

} catch (Exception $e) {
    echo "❌ Error al conectar con Gemini:\n";
    echo "   " . $e->getMessage() . "\n\n";
    
    if (strpos($e->getMessage(), '400') !== false) {
        echo "💡 Sugerencia: Verifica que la API Key sea válida\n";
    } elseif (strpos($e->getMessage(), '429') !== false) {
        echo "💡 Sugerencia: Has excedido el límite de peticiones, espera un momento\n";
    } elseif (strpos($e->getMessage(), 'timeout') !== false) {
        echo "💡 Sugerencia: La API tardó demasiado en responder, intenta nuevamente\n";
    }
}

echo "🏁 Prueba finalizada.\n";
