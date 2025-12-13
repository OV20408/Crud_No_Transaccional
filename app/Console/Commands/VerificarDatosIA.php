<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerificarDatosIA extends Command
{
    protected $signature = 'ia:verificar';
    protected $description = 'Verificar si hay cursos y necesidades disponibles para las recomendaciones de IA';

    public function handle()
    {
        $this->info('==============================================');
        $this->info('VERIFICACIÓN DE DATOS PARA RECOMENDACIONES IA');
        $this->info('==============================================');
        $this->newLine();

        // 1. Verificar cursos
        $this->info('1. CURSOS DISPONIBLES:');
        $this->line('   ------------------');
        
        $cursos = DB::table('curso')
            ->join('capacitacion', 'curso.id_capacitacion', '=', 'capacitacion.id')
            ->select('curso.id', 'curso.nombre', 'capacitacion.nombre as capacitacion')
            ->get();

        if ($cursos->isEmpty()) {
            $this->error('   ❌ NO HAY CURSOS EN LA BASE DE DATOS');
            $this->warn('   → La IA NO puede recomendar cursos porque no hay opciones disponibles.');
            $this->warn('   → Solución: Crear cursos en Admin > Capacitaciones > Cursos');
        } else {
            $this->info("   ✅ Total de cursos: " . $cursos->count());
            foreach ($cursos as $curso) {
                $this->line("      • ID: {$curso->id} - {$curso->nombre} (Capacitación: {$curso->capacitacion})");
            }
        }
        
        $this->newLine();

        // 2. Verificar necesidades
        $this->info('2. NECESIDADES DISPONIBLES:');
        $this->line('   ------------------------');
        
        $necesidades = DB::table('necesidad')
            ->select('id', 'tipo', 'descripcion')
            ->get();

        if ($necesidades->isEmpty()) {
            $this->error('   ❌ NO HAY NECESIDADES EN LA BASE DE DATOS');
            $this->warn('   → La IA NO puede evaluar aptitud porque no hay necesidades que atender.');
            $this->warn('   → Solución: Crear necesidades en Admin > Gestión de Necesidades');
        } else {
            $this->info("   ✅ Total de necesidades: " . $necesidades->count());
            foreach ($necesidades as $necesidad) {
                $desc = strlen($necesidad->descripcion) > 50 
                    ? substr($necesidad->descripcion, 0, 50) . '...' 
                    : $necesidad->descripcion;
                $this->line("      • ID: {$necesidad->id} - {$necesidad->tipo}");
                $this->line("        Descripción: {$desc}");
            }
        }
        
        $this->newLine();

        // 3. Verificar API Keys
        $this->info('3. CONFIGURACIÓN DE API KEYS:');
        $this->line('   --------------------------');

        $apiKeyCursos = env('GOOGLE_GEMINI_API_KEY_CURSOS');
        $apiKeyNecesidades = env('GOOGLE_GEMINI_API_KEY_NECESIDADES');

        if (empty($apiKeyCursos)) {
            $this->error('   ❌ GOOGLE_GEMINI_API_KEY_CURSOS no está configurada');
        } else {
            $this->info('   ✅ GOOGLE_GEMINI_API_KEY_CURSOS: ' . substr($apiKeyCursos, 0, 20) . '...');
        }

        if (empty($apiKeyNecesidades)) {
            $this->error('   ❌ GOOGLE_GEMINI_API_KEY_NECESIDADES no está configurada');
        } else {
            $this->info('   ✅ GOOGLE_GEMINI_API_KEY_NECESIDADES: ' . substr($apiKeyNecesidades, 0, 20) . '...');
        }

        $this->newLine();

        // 4. Resumen y recomendaciones
        $this->info('==============================================');
        $this->info('RESUMEN Y RECOMENDACIONES:');
        $this->info('==============================================');

        $problemas = [];

        if ($cursos->isEmpty()) {
            $problemas[] = 'CRÍTICO: No hay cursos creados - La IA no puede recomendar cursos';
        }

        if ($necesidades->isEmpty()) {
            $problemas[] = 'CRÍTICO: No hay necesidades creadas - La IA no puede evaluar aptitud';
        }

        if (empty($apiKeyCursos)) {
            $problemas[] = 'ERROR: Falta API Key para cursos en el docker-compose.yml';
        }

        if (empty($apiKeyNecesidades)) {
            $problemas[] = 'ERROR: Falta API Key para necesidades en el docker-compose.yml';
        }

        if (empty($problemas)) {
            $this->info('✅ TODO ESTÁ CONFIGURADO CORRECTAMENTE');
            $this->info('   Las recomendaciones de IA deberían funcionar.');
        } else {
            $this->warn('⚠️  SE ENCONTRARON LOS SIGUIENTES PROBLEMAS:');
            $this->newLine();
            foreach ($problemas as $problema) {
                $this->error('   • ' . $problema);
            }
            $this->newLine();
            $this->warn('ACCIONES REQUERIDAS:');
            if ($cursos->isEmpty() || $necesidades->isEmpty()) {
                $this->line('→ Debes crear cursos y/o necesidades desde el panel de administración');
                $this->line('→ La IA necesita datos existentes para poder hacer recomendaciones');
            }
            if (empty($apiKeyCursos) || empty($apiKeyNecesidades)) {
                $this->line('→ Verifica que el docker-compose.yml tenga las API Keys configuradas');
                $this->line('→ Reinicia los contenedores después de editar docker-compose.yml');
            }
        }

        $this->newLine();
        $this->info('==============================================');

        return 0;
    }
}
