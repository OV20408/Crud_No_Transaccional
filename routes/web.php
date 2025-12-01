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
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ConsultaController;use Illuminate\Support\Facades\DB;
use App\Http\Controllers\VoluntarioController;
use App\Http\Controllers\AdministradorController;
use App\Http\Controllers\AyudasSolicitadasController;
use App\Http\Controllers\EvaluacionVoluntarioController;
use App\Models\Consulta;
use App\Events\ConsultaRespondida;



Route::post(
    'voluntarios/{id}/necesidades/asignar',
    [VoluntarioController::class, 'asignarNecesidad']
)->name('voluntarios.necesidades.asignar');



Route::get('/administradores', [AdministradorController::class, 'index'])
    ->name('administradores.index');

Route::get('/administradores/create', [AdministradorController::class, 'create'])
    ->name('administradores.create');

Route::post('/administradores', [AdministradorController::class, 'store'])
    ->name('administradores.store');

Route::patch('/administradores/{id}/toggle-estado', [AdministradorController::class, 'toggleEstado'])
    ->name('administradores.toggle-estado');
    

    #----------------------------------------------------------

/* Route::get('/chat-consulta', function () {
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

    return view('chat-consulta.index', compact('mensajes'));
}); */

Route::get('/chat-consulta', function () {
    $esEmergencia = request()->query('emergencia') == '1';
    $voluntarioId = request()->query('voluntario_id');
    $ayudaId      = request()->query('ayuda_id');

    // Consultar mensajes normales
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

    // Si viene de emergencia y no hay mensajes de ese voluntario relacionados
    if ($esEmergencia && $voluntarioId && $ayudaId) {
        $marcador = "🚨 [EMERGENCIA #{$ayudaId}]";
        
        // Buscar si ya existe un mensaje de esta emergencia
        $existeMensajeEmergencia = $mensajes->contains(function ($m) use ($marcador, $voluntarioId) {
            return $m->voluntario_id == $voluntarioId 
                && strpos($m->texto, $marcador) !== false;
        });

        if (!$existeMensajeEmergencia) {
            // Obtener datos de la ayuda
            $ayuda = DB::table('solicitudes_ayuda')
                ->where('id', $ayudaId)
                ->first();

            if ($ayuda) {
                // Crear mensaje inicial automático
                DB::table('chat_mensajes')->insert([
                    'voluntario_id' => $voluntarioId,
                    'de'            => 'admin',
                    'texto'         => "{$marcador} Hemos recibido tu solicitud: \"{$ayuda->descripcion}\". Un equipo está revisando tu caso. Responde aquí cualquier duda.",
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);

                // Actualizar estado de la solicitud
                DB::table('solicitudes_ayuda')
                    ->where('id', $ayudaId)
                    ->update([
                        'estado'     => 'en progreso',
                        'updated_at' => now(),
                    ]);

                // Recargar mensajes
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

#------------------------------------------------- HACIA ARRIBA ES LA COMS DE WEB A MOVIL

Route::post(
    'voluntarios/{id}/capacitaciones/asignar',
    [VoluntarioController::class, 'asignarCapacitacion']
)->name('voluntarios.capacitaciones.asignar');



Route::get('/ayudas_solicitadas', [AyudasSolicitadasController::class, 'index'])
    ->name('ayudas_solicitadas.index');

Route::resource('voluntarios', VoluntarioController::class);
Route::resource('consultas-web', ConsultaController::class);
Route::resource('roles', RolController::class);
Route::resource('capacitaciones', CapacitacionController::class);
Route::resource('necesidades', NecesidadController::class);
Route::resource('test', TestController::class);
Route::resource('universidades', UniversidadController::class);
Route::resource('historial_clinico', HistorialClinicoController::class);
Route::resource('curso', CursoController::class);
Route::resource('etapas', EtapaController::class);
Route::resource('pregunta', PreguntaController::class);
Route::resource('reportes', ReporteController::class);
Route::resource('evaluacion', EvaluacionController::class);
Route::resource('respuesta', RespuestaController::class);
Route::resource('progreso-voluntario', ProgresoVoluntarioController::class);
Route::view('evaluacion_pruebas', 'evaluacion_pruebas.index')->name('evaluacion_pruebas');

// Rutas para envío de formulario de evaluación a voluntarios
Route::post('/voluntarios/{id}/enviar-formulario', [EvaluacionVoluntarioController::class, 'enviarInvitacion'])
    ->name('voluntarios.enviar-formulario');

// Ruta pública para que el voluntario acceda a la evaluación (sin auth)
Route::get('/evaluacion-voluntario/{token}', [EvaluacionVoluntarioController::class, 'mostrarEvaluacion'])
    ->name('evaluacion-voluntario.mostrar');

Route::post('/evaluacion-voluntario/{token}/procesar', [EvaluacionVoluntarioController::class, 'procesarEvaluacion'])
    ->name('evaluacion-voluntario.procesar');

// Historial de encuestas de un voluntario
Route::get('/voluntarios/{id}/historial-encuestas', [EvaluacionVoluntarioController::class, 'historialEncuestas'])
    ->name('voluntarios.historial-encuestas');

// API: Datos actualizados del voluntario (para refresh automático)
Route::get('/voluntarios/{id}/datos-actualizados', [VoluntarioController::class, 'getDatosActualizados'])
    ->name('voluntarios.datos-actualizados');

// Ver detalle de un reporte/encuesta realizada (físico o emocional)
Route::get('/reporte/{id}/{tipo}', [EvaluacionVoluntarioController::class, 'verReporte'])
    ->name('reporte.ver')
    ->where('tipo', 'fisico|emocional');



Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();

// Ruta GET adicional para logout (alternativa a POST)
Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout.get');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
