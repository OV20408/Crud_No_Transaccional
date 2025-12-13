<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RolController;
use App\Http\Controllers\CapacitacionController;
use App\Http\Controllers\NecesidadController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UniversidadController;
use App\Http\Controllers\HistorialClinicoController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\EtapaController;
use App\Http\Controllers\PreguntaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\EvaluacionController;
use App\Http\Controllers\RespuestaController;
use App\Http\Controllers\ProgresoVoluntarioController;
use App\Http\Controllers\ConsultaController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\VoluntarioController;
use App\Http\Controllers\AdministradorController;
use App\Http\Controllers\AyudasSolicitadasController;
use App\Http\Controllers\EvaluacionVoluntarioController;
use App\Http\Controllers\CertificadoController;


Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();

Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout.get');

// Evaluación de voluntarios 
Route::get('/evaluacion-voluntario/{token}', [EvaluacionVoluntarioController::class, 'mostrarEvaluacion'])
    ->name('evaluacion-voluntario.mostrar');

Route::post('/evaluacion-voluntario/{token}/procesar', [EvaluacionVoluntarioController::class, 'procesarEvaluacion'])
    ->name('evaluacion-voluntario.procesar');

Route::middleware(['auth'])->group(function () {
    
    // Ruta Home (accesible para cualquier usuario autenticado)
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    //RUTAS SOLO PARA ADMINISTRADORES (Web)
    Route::middleware(['role:Administrador'])->group(function () {
        
        // Gestión de administradores
        Route::get('/administradores', [AdministradorController::class, 'index'])
            ->name('administradores.index');
        Route::get('/administradores/create', [AdministradorController::class, 'create'])
            ->name('administradores.create');
        Route::post('/administradores', [AdministradorController::class, 'store'])
            ->name('administradores.store');
        Route::patch('/administradores/{id}/toggle-estado', [AdministradorController::class, 'toggleEstado'])
            ->name('administradores.toggle-estado');

            //inactivos coso
            Route::get('/voluntarios/inactivos', [VoluntarioController::class, 'inactivos'])
            ->name(name: 'voluntarios.inactivos');

        // Gestión de voluntarios
            Route::resource('voluntarios', VoluntarioController::class);
        
            Route::get('/voluntarios/{id}/historial-pdf', [VoluntarioController::class, 'descargarHistorialPDF'])
            ->name('voluntarios.historial.pdf');

            Route::get('/voluntarios/{id}/capacitaciones-pdf', [VoluntarioController::class, 'descargarCapacitacionesPDF'])
            ->name('voluntarios.capacitaciones.pdf');

            Route::get('/voluntarios/{id}/necesidades-pdf', [VoluntarioController::class, 'descargarNecesidadesPDF'])
            ->name('voluntarios.necesidades.pdf');

           

        // Cambiar estado del voluntario
            Route::post('/voluntarios/{id}/cambiar-estado', [VoluntarioController::class, 'cambiarEstado'])
            ->name('voluntarios.cambiar-estado');

            Route::post('voluntarios/{id}/necesidades/asignar', [VoluntarioController::class, 'asignarNecesidad'])
            ->name('voluntarios.necesidades.asignar');
        
            Route::post('voluntarios/{id}/capacitaciones/asignar', [VoluntarioController::class, 'asignarCapacitacion'])
            ->name('voluntarios.capacitaciones.asignar');
        
            Route::post('voluntarios/{id}/cursos/asignar', [VoluntarioController::class, 'asignarCurso'])
            ->name('voluntarios.cursos.asignar');
        
            Route::get('/voluntarios/{voluntarioId}/reporte/{reporteId}/{tipo}/marcar-visto', 
            [VoluntarioController::class, 'marcarReporteVisto'])
            ->name('voluntarios.marcar-reporte-visto')
            ->where('tipo', 'fisico|emocional');
        
            Route::get('/voluntarios/{id}/datos-actualizados', [VoluntarioController::class, 'getDatosActualizados'])
            ->name('voluntarios.datos-actualizados');

        // Gestión de capacitaciones
        Route::resource('capacitaciones', CapacitacionController::class);
        
        // Gestión de certificados
        Route::post('/certificados/forzar/{idUsuario}/{idCapacitacion}', [CertificadoController::class, 'forzarRegeneracion']);
        Route::post('/certificados/generar/{idUsuario}/{idCapacitacion}', [CertificadoController::class, 'generarCertificado']);
        Route::get('/certificados/descargar/{id}', [CertificadoController::class, 'descargarCertificado'])
            ->name('certificados.descargar');

        // Gestión de roles
        Route::resource('roles', RolController::class);
        
        // Gestión de necesidades
        Route::resource('necesidades', NecesidadController::class);
        
        // Gestión de tests
        Route::resource('test', TestController::class);
        
        // Gestión de universidades
        Route::resource('universidades', UniversidadController::class);
        
        // Gestión de historial clínico
        Route::resource('historial_clinico', HistorialClinicoController::class);
        
        // Gestión de cursos
        Route::resource('curso', CursoController::class);
        
        // Gestión de etapas
        Route::resource('etapas', EtapaController::class);
        
        // Gestión de preguntas
        Route::resource('pregunta', PreguntaController::class);
        
        // Gestión de reportes
        Route::resource('reportes', ReporteController::class);
        
        // Gestión de evaluaciones
        Route::resource('evaluacion', EvaluacionController::class);
        Route::view('evaluacion_pruebas', 'evaluacion_pruebas.index')->name('evaluacion_pruebas');
        
        // Gestión de respuestas
        Route::resource('respuesta', RespuestaController::class);
        
        // Gestión de progreso de voluntarios
        Route::resource('progreso-voluntario', ProgresoVoluntarioController::class);
        
        // Consultas web
        Route::resource('consultas-web', ConsultaController::class);
        
        // Chat con voluntarios (comunicación web-móvil)
        Route::get('/chat-consulta', function () {
            $esEmergencia = request()->query('emergencia') == '1';
            $voluntarioId = request()->query('voluntario_id');
            $ayudaId      = request()->query('ayuda_id');

            $mensajes = DB::table('chat_mensajes')
                ->join('usuario', 'usuario.id_usuario', '=', 'chat_mensajes.voluntario_id')
                ->select(
                    'chat_mensajes.*',
                    'usuario.nombres',
                    'usuario.apellidos',
                    'usuario.ci'
                )
                ->orderBy('chat_mensajes.created_at', 'asc')
                ->get();

            if ($esEmergencia && $voluntarioId && $ayudaId) {
                $marcador = "🚨 [EMERGENCIA #{$ayudaId}]";
                
                $existeMensajeEmergencia = $mensajes->contains(function ($m) use ($marcador, $voluntarioId) {
                    return $m->voluntario_id == $voluntarioId 
                        && strpos($m->texto, $marcador) !== false;
                });

                if (!$existeMensajeEmergencia) {
                    $ayuda = DB::table('solicitudes_ayuda')
                        ->where('id', $ayudaId)
                        ->first();

                    if ($ayuda) {
                        DB::table('chat_mensajes')->insert([
                            'voluntario_id' => $voluntarioId,
                            'de'            => 'admin',
                            'texto'         => "{$marcador} Hemos recibido tu solicitud: \"{$ayuda->descripcion}\". Un equipo está revisando tu caso. Responde aquí cualquier duda.",
                            'created_at'    => now(),
                            'updated_at'    => now(),
                        ]);

                        DB::table('solicitudes_ayuda')
                            ->where('id', $ayudaId)
                            ->update([
                                'estado'     => 'en progreso',
                                'updated_at' => now(),
                            ]);

                        $mensajes = DB::table('chat_mensajes')
                            ->join('usuario', 'usuario.id_usuario', '=', 'chat_mensajes.voluntario_id')
                            ->select(
                                'chat_mensajes.*',
                                'usuario.nombres',
                                'usuario.apellidos',
                                'usuario.ci'
                            )
                            ->orderBy('chat_mensajes.created_at', 'asc')
                            ->get();
                    }
                }
            }

            return view('chat-consulta.index', compact('mensajes', 'voluntarioId', 'ayudaId', 'esEmergencia'));
        })->name('chat.consulta');

        // Ayudas solicitadas
        Route::get('/ayudas_solicitadas', [AyudasSolicitadasController::class, 'index'])
            ->name('ayudas_solicitadas.index');

        // Evaluaciones de voluntarios
        Route::post('/voluntarios/{id}/enviar-formulario', [EvaluacionVoluntarioController::class, 'enviarInvitacion'])
            ->name('voluntarios.enviar-formulario');
        Route::get('/voluntarios/{id}/historial-encuestas', [EvaluacionVoluntarioController::class, 'historialEncuestas'])
            ->name('voluntarios.historial-encuestas');
        Route::get('/reporte/{id}/{tipo}', [EvaluacionVoluntarioController::class, 'verReporte'])
            ->name('reporte.ver')
            ->where('tipo', 'fisico|emocional');

        // API interna para certificados (solo admin desde web)
        Route::get('/api/certificados/{idUsuario}/{idCapacitacion}', function($idUsuario, $idCapacitacion) {
            $certificado = DB::table('certificados')
                ->where('id_usuario', $idUsuario)
                ->where('id_capacitacion', $idCapacitacion)
                ->where('estado', 'activo')
                ->first();
            
            return response()->json([
                'success' => !!$certificado,
                'certificado' => $certificado
            ]);
        });
    });
});

// ========== HELPDESK WIDGET ==========
// Ruta generada por: php artisan helpdeskwidget:install
Route::get('helpdesk', function () {
    return view('helpdesk');
})->name('helpdesk')->middleware('auth');
