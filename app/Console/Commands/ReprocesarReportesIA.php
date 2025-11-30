<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reporte;
use App\Services\IAService;
use Illuminate\Support\Facades\Log;

class ReprocesarReportesIA extends Command
{
    protected $signature = 'reportes:reprocesar-ia {--all : Reprocesar todos los reportes} {--id= : ID específico de reporte}';
    protected $description = 'Reprocesa los reportes existentes enviándolos a la IA para obtener análisis';

    protected IAService $iaService;

    public function __construct(IAService $iaService)
    {
        parent::__construct();
        $this->iaService = $iaService;
    }

    public function handle()
    {
        $this->info('Iniciando reprocesamiento de reportes con IA...');
        
        // Obtener reportes a procesar
        if ($this->option('id')) {
            $reportes = Reporte::where('id', $this->option('id'))->get();
        } elseif ($this->option('all')) {
            $reportes = Reporte::all();
        } else {
            // Por defecto, solo los que no han sido procesados por IA
            $reportes = Reporte::where('estado_general', '!=', 'Procesado por IA')
                ->orWhereNull('estado_general')
                ->get();
        }

        if ($reportes->isEmpty()) {
            $this->warn('No hay reportes para procesar.');
            return 0;
        }

        $this->info("Se procesarán {$reportes->count()} reportes.");
        
        $bar = $this->output->createProgressBar($reportes->count());
        $bar->start();

        $exitosos = 0;
        $fallidos = 0;

        foreach ($reportes as $reporte) {
            try {
                // Obtener el texto actual (respuestas del usuario)
                $evaluacionFisica = $reporte->resumen_fisico ?? '';
                $evaluacionEmocional = $reporte->resumen_emocional ?? '';

                if (empty($evaluacionFisica) && empty($evaluacionEmocional)) {
                    $bar->advance();
                    continue;
                }

                // Llamar a la IA
                $resultadoIA = $this->iaService->generarEvaluacionCompleta(
                    $evaluacionFisica,
                    $evaluacionEmocional
                );

                if ($resultadoIA['success']) {
                    // Actualizar el reporte con las respuestas de la IA
                    $reporte->update([
                        'resumen_fisico' => $resultadoIA['fisico']['respuesta'] ?? $evaluacionFisica,
                        'resumen_emocional' => $resultadoIA['emocional']['respuesta'] ?? $evaluacionEmocional,
                        'estado_general' => 'Procesado por IA'
                    ]);
                    $exitosos++;
                } else {
                    Log::warning("Error procesando reporte {$reporte->id}", $resultadoIA);
                    $fallidos++;
                }

            } catch (\Exception $e) {
                Log::error("Excepción procesando reporte {$reporte->id}: " . $e->getMessage());
                $fallidos++;
            }

            $bar->advance();
            
            // Pequeña pausa para no saturar la API
            usleep(500000); // 0.5 segundos
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Proceso completado:");
        $this->info("  ✓ Exitosos: {$exitosos}");
        if ($fallidos > 0) {
            $this->warn("  ✗ Fallidos: {$fallidos}");
        }

        return 0;
    }
}
