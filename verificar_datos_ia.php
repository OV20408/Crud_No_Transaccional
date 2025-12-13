<?php

/**
 * Script para verificar si hay datos necesarios para las recomendaciones de IA
 * Ejecutar con: php verificar_datos_ia.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "==============================================\n";
echo "VERIFICACIÓN DE DATOS PARA RECOMENDACIONES IA\n";
echo "==============================================\n\n";

// 1. Verificar cursos
echo "1. CURSOS DISPONIBLES:\n";
echo "   ------------------\n";
$cursos = DB::table('curso')
    ->join('capacitacion', 'curso.id_capacitacion', '=', 'capacitacion.id')
    ->select('curso.id', 'curso.nombre', 'capacitacion.nombre as capacitacion')
    ->get();

if ($cursos->isEmpty()) {
    echo "   ❌ NO HAY CURSOS EN LA BASE DE DATOS\n";
    echo "   → La IA NO puede recomendar cursos porque no hay opciones disponibles.\n";
    echo "   → Solución: Crear cursos en Admin > Capacitaciones > Cursos\n\n";
} else {
    echo "   ✅ Total de cursos: " . $cursos->count() . "\n";
    foreach ($cursos as $curso) {
        echo "      • ID: {$curso->id} - {$curso->nombre} (Capacitación: {$curso->capacitacion})\n";
    }
    echo "\n";
}

// 2. Verificar necesidades
echo "2. NECESIDADES DISPONIBLES:\n";
echo "   ------------------------\n";
$necesidades = DB::table('necesidad')
    ->select('id', 'tipo', 'descripcion')
    ->get();

if ($necesidades->isEmpty()) {
    echo "   ❌ NO HAY NECESIDADES EN LA BASE DE DATOS\n";
    echo "   → La IA NO puede evaluar aptitud porque no hay necesidades que atender.\n";
    echo "   → Solución: Crear necesidades en Admin > Gestión de Necesidades\n\n";
} else {
    echo "   ✅ Total de necesidades: " . $necesidades->count() . "\n";
    foreach ($necesidades as $necesidad) {
        $desc = strlen($necesidad->descripcion) > 50 
            ? substr($necesidad->descripcion, 0, 50) . '...' 
            : $necesidad->descripcion;
        echo "      • ID: {$necesidad->id} - {$necesidad->tipo}\n";
        echo "        Descripción: {$desc}\n";
    }
    echo "\n";
}

// 3. Verificar API Keys
echo "3. CONFIGURACIÓN DE API KEYS:\n";
echo "   --------------------------\n";

$apiKeyCursos = env('GOOGLE_GEMINI_API_KEY_CURSOS');
$apiKeyNecesidades = env('GOOGLE_GEMINI_API_KEY_NECESIDADES');

if (empty($apiKeyCursos)) {
    echo "   ❌ GOOGLE_GEMINI_API_KEY_CURSOS no está configurada\n";
} else {
    echo "   ✅ GOOGLE_GEMINI_API_KEY_CURSOS: " . substr($apiKeyCursos, 0, 20) . "...\n";
}

if (empty($apiKeyNecesidades)) {
    echo "   ❌ GOOGLE_GEMINI_API_KEY_NECESIDADES no está configurada\n";
} else {
    echo "   ✅ GOOGLE_GEMINI_API_KEY_NECESIDADES: " . substr($apiKeyNecesidades, 0, 20) . "...\n";
}

echo "\n";

// 4. Resumen y recomendaciones
echo "==============================================\n";
echo "RESUMEN Y RECOMENDACIONES:\n";
echo "==============================================\n";

$problemas = [];

if ($cursos->isEmpty()) {
    $problemas[] = "• CRÍTICO: No hay cursos creados - La IA no puede recomendar cursos";
}

if ($necesidades->isEmpty()) {
    $problemas[] = "• CRÍTICO: No hay necesidades creadas - La IA no puede evaluar aptitud";
}

if (empty($apiKeyCursos)) {
    $problemas[] = "• ERROR: Falta API Key para cursos en el docker-compose.yml";
}

if (empty($apiKeyNecesidades)) {
    $problemas[] = "• ERROR: Falta API Key para necesidades en el docker-compose.yml";
}

if (empty($problemas)) {
    echo "✅ TODO ESTÁ CONFIGURADO CORRECTAMENTE\n";
    echo "   Las recomendaciones de IA deberían funcionar.\n";
} else {
    echo "⚠️  SE ENCONTRARON LOS SIGUIENTES PROBLEMAS:\n\n";
    foreach ($problemas as $problema) {
        echo "   $problema\n";
    }
    echo "\n";
    echo "ACCIONES REQUERIDAS:\n";
    if ($cursos->isEmpty() || $necesidades->isEmpty()) {
        echo "→ Debes crear cursos y/o necesidades desde el panel de administración\n";
        echo "→ La IA necesita datos existentes para poder hacer recomendaciones\n";
    }
    if (empty($apiKeyCursos) || empty($apiKeyNecesidades)) {
        echo "→ Verifica que el docker-compose.yml tenga las API Keys configuradas\n";
        echo "→ Reinicia los contenedores después de editar docker-compose.yml\n";
    }
}

echo "\n==============================================\n";
