<?php
/**
 * Script de prueba para verificar la API Key de Google Gemini - Necesidades
 * Modelo: gemini-2.5-flash
 */

// API Key a probar (RECIÉN CREADA)
$apiKey = 'AIzaSyA6EORDnl4ZMLxIuBXt4P_pz4ztyk6io3I';

// Endpoint de la API de Gemini - Modelo 2.5 flash
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

// Datos de prueba
$data = [
    'contents' => [
        [
            'parts' => [
                [
                    'text' => 'Hola, ¿puedes responder con un simple "API funcionando correctamente"?'
                ]
            ]
        ]
    ],
    'generationConfig' => [
        'temperature' => 0.7,
        'maxOutputTokens' => 100
    ]
];

echo "==========================================\n";
echo "PRUEBA DE API KEY DE GEMINI - NECESIDADES\n";
echo "Modelo: gemini-2.5-flash\n";
echo "==========================================\n\n";

echo "API Key: " . substr($apiKey, 0, 20) . "...\n";
echo "URL: " . $url . "\n\n";

echo "Enviando petición...\n\n";

// Inicializar cURL
$ch = curl_init($url);

// Configurar opciones de cURL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Solo para desarrollo

// Ejecutar la petición
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);

curl_close($ch);

// Mostrar resultados
echo "==========================================\n";
echo "RESULTADOS\n";
echo "==========================================\n\n";

echo "Código HTTP: " . $httpCode . "\n\n";

if ($curlError) {
    echo "❌ ERROR DE CURL:\n";
    echo $curlError . "\n\n";
}

if ($httpCode === 200) {
    echo "✅ ESTADO: Conexión exitosa\n\n";
    
    $jsonResponse = json_decode($response, true);
    
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "📝 RESPUESTA COMPLETA:\n";
        echo json_encode($jsonResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
        
        // Extraer el texto de la respuesta
        if (isset($jsonResponse['candidates'][0]['content']['parts'][0]['text'])) {
            $textoRespuesta = $jsonResponse['candidates'][0]['content']['parts'][0]['text'];
            echo "💬 TEXTO GENERADO:\n";
            echo $textoRespuesta . "\n\n";
            echo "✅ LA API KEY ESTÁ FUNCIONANDO CORRECTAMENTE\n";
        } else {
            echo "⚠️ Respuesta recibida pero sin texto generado\n";
        }
    } else {
        echo "❌ Error al decodificar JSON:\n";
        echo json_last_error_msg() . "\n\n";
        echo "Respuesta raw:\n";
        echo $response . "\n";
    }
} else {
    echo "❌ ESTADO: La petición falló\n\n";
    echo "📄 RESPUESTA DEL SERVIDOR:\n";
    echo $response . "\n\n";
    
    $jsonResponse = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE && isset($jsonResponse['error'])) {
        echo "❌ ERROR DE LA API:\n";
        echo "Mensaje: " . ($jsonResponse['error']['message'] ?? 'No especificado') . "\n";
        echo "Código: " . ($jsonResponse['error']['code'] ?? 'No especificado') . "\n";
        echo "Estado: " . ($jsonResponse['error']['status'] ?? 'No especificado') . "\n\n";
        
        if (isset($jsonResponse['error']['details'])) {
            echo "Detalles adicionales:\n";
            echo json_encode($jsonResponse['error']['details'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        }
    }
}

echo "\n==========================================\n";
echo "FIN DE LA PRUEBA\n";
echo "==========================================\n";
