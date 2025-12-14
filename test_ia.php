<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\IAService;

$iaService = app(IAService::class);

echo "=== PRUEBA DE IA RECOMENDACION DE CURSOS ===\n\n";

$cursos = [
    ['id' => 1, 'nombre' => 'RCP Básico', 'descripcion' => 'Reanimación cardiopulmonar básica', 'capacitacion_nombre' => 'Primeros Auxilios'],
    ['id' => 2, 'nombre' => 'Mindfulness para Bomberos', 'descripcion' => 'Técnicas de relajación y manejo emocional', 'capacitacion_nombre' => 'Manejo de Estrés'],
];

$result = $iaService->recomendarCursos(
    'El voluntario presenta fatiga extrema, dolor de pecho frecuente y dificultad respiratoria',
    'El voluntario muestra signos de ansiedad severa, estrés postraumático y pensamientos intrusivos',
    $cursos,
    'Juan Perez'
);

echo "Resultado recomendación de cursos:\n";
print_r($result);

echo "\n\n=== PRUEBA DE IA APTITUD NECESIDADES ===\n\n";

$necesidades = [
    ['id' => 1, 'tipo' => 'Incendio Forestal', 'descripcion' => 'Combate de incendios en áreas forestales'],
    ['id' => 2, 'tipo' => 'Rescate Urbano', 'descripcion' => 'Rescate en edificios y estructuras urbanas'],
    ['id' => 3, 'tipo' => 'Atención Médica', 'descripcion' => 'Primeros auxilios y atención pre-hospitalaria'],
];

$resultAptitud = $iaService->evaluarAptitudNecesidades(
    'El voluntario presenta fatiga moderada pero sin síntomas graves',
    'El voluntario está emocionalmente estable',
    $necesidades
);

echo "Resultado aptitud necesidades:\n";
print_r($resultAptitud);

echo "\n=== FIN DE PRUEBAS ===\n";
