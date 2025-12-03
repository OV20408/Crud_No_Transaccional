<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\IAService;
use App\Models\Necesidad;
use App\Models\AptitudNecesidad;

$iaService = new IAService();

// Datos del reporte ID 37
$resumenFisico = "Valoración física con indicadores estables. No se identifican síntomas significativos. La condición física permite el desarrollo normal de actividades. Se sugiere mantener hábitos de vida saludables y monitoreo personal continuo de cualquier síntoma nuevo que pudiera surgir.";

$resumenEmocional = "Perfil emocional con manifestaciones moderadas de desgaste. Se observa ansiedad moderada persistente. Apoyo psicológico preventivo sugerido. Implementar estrategias de regulación emocional, mantener actividades placenteras regulares y fortalecer comunicación con red de apoyo cercana. Adicionalmente: Practicar técnicas de respiración y relajación. Considerar terapia cognitivo-conductual. Técnicas de anclaje y mindfulness pueden ayudar. Considerar terapia EMDR si persisten.";

// Obtener necesidades
$necesidades = Necesidad::select('id', 'tipo', 'descripcion')->get()->toArray();

echo "=== EVALUANDO APTITUD PARA NECESIDADES ===\n";
echo "Voluntario ID: 3\n";
echo "Reporte ID: 37\n\n";
echo "Resumen Físico: " . substr($resumenFisico, 0, 100) . "...\n";
echo "Resumen Emocional: " . substr($resumenEmocional, 0, 100) . "...\n\n";
echo "Necesidades disponibles: " . count($necesidades) . "\n\n";

$resultado = $iaService->evaluarAptitudNecesidades($resumenFisico, $resumenEmocional, $necesidades);

if ($resultado['success']) {
    echo "✅ Evaluación exitosa\n\n";
    echo "NIVEL DE APTITUD: " . $resultado['nivel_aptitud'] . "\n";
    echo "RAZÓN: " . $resultado['razon'] . "\n";
    echo "NECESIDADES APTAS: " . json_encode($resultado['necesidades_aptas']) . "\n\n";
    
    // Guardar en base de datos
    AptitudNecesidad::where('id_voluntario', 3)->delete();
    
    $aptitud = AptitudNecesidad::create([
        'id_voluntario' => 3,
        'id_necesidad' => null,
        'id_reporte' => 37,
        'nivel_aptitud' => $resultado['nivel_aptitud'],
        'razon_ia' => $resultado['razon'],
        'necesidades_recomendadas' => json_encode($resultado['necesidades_aptas']),
        'estado' => 'activo'
    ]);
    
    echo "✅ Guardado en base de datos (ID: {$aptitud->id})\n";
    
    // Mostrar nombres de necesidades recomendadas
    if ($resultado['nivel_aptitud'] === 'APTO_TODAS') {
        echo "\n🎯 Puede realizar TODAS las necesidades\n";
    } else if ($resultado['nivel_aptitud'] === 'APTO_ALGUNAS' && !empty($resultado['necesidades_aptas'])) {
        echo "\n🎯 Necesidades recomendadas:\n";
        foreach ($resultado['necesidades_aptas'] as $idNecesidad) {
            $necesidad = Necesidad::find($idNecesidad);
            if ($necesidad) {
                echo "   - [{$idNecesidad}] {$necesidad->tipo}\n";
            }
        }
    } else if ($resultado['nivel_aptitud'] === 'NO_APTO') {
        echo "\n❌ NO APTO - No se recomienda asignar necesidades\n";
    }
} else {
    echo "❌ Error: " . $resultado['error'] . "\n";
}
