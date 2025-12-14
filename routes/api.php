<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UniversidadApiController;
use App\Http\Controllers\Api\ConsultaApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\VoluntarioApiController;
use App\Http\Controllers\Api\UsuarioApiController;
use App\Http\Controllers\Api\ChatMensajeApiController;
use App\Http\Controllers\Api\CapacitacionApiController;
use App\Http\Controllers\Api\SolicitudAyudaApiController;
use App\Http\Controllers\Api\EtapaApiController;
use App\Http\Controllers\Api\ReporteApiController;
use App\Http\Controllers\Api\CursoApiController;
use App\Http\Controllers\Api\CursoSyncController;
use App\Http\Controllers\Api\UsuarioSyncController;
use App\Http\Controllers\TrazabilidadController;
use App\Http\Controllers\Auth\RegistroSimpleController;

Route::get('registro/ci/{ci}', [RegistroSimpleController::class, 'showByCi']);

// Asignar universidad a evaluación vía reporte
Route::post('/evaluaciones/asignar-universidad/{reporteId}', [App\Http\Controllers\Api\EvaluacionApiController::class, 'asignarUniversidad']);


Route::put('/usuarios/{id}/estado', [UsuarioApiController::class, 'updateEstado']);
Route::post('/sync/cursos', [CursoSyncController::class, 'syncStore']);



Route::prefix('sync')->group(function () {

    // Cursos
    Route::get('/cursos/search', [CursoSyncController::class, 'search']);

    // Usuarios
    Route::get('/usuarios/ci/{ci}', [UsuarioSyncController::class, 'buscarPorCi']);
    Route::put('/usuarios/{id}/estado', [UsuarioSyncController::class, 'actualizarEstado']);
});



// ✅ AGREGAR ESTA LÍNEA
Route::get('/cursos/{cursoId}/progreso/{voluntarioId}', [CursoApiController::class, 'obtenerProgresoVoluntario']);
Route::patch('/solicitudes-ayuda/{id}/resolver', [SolicitudAyudaApiController::class, 'marcarResuelta']);

Route::patch('/etapas/{etapa}/estado', [EtapaApiController::class, 'toggleEstado'])
    ->middleware('auth:sanctum');
use App\Http\Controllers\Api\EvaluacionIAController;

// ==================== EVALUACIONES CON IA ====================
Route::post('/evaluaciones/procesar', [EvaluacionIAController::class, 'procesarEvaluacion']);
Route::get('/evaluaciones/historial/{idUsuario}', [EvaluacionIAController::class, 'historialVoluntario']);

Route::get('/solicitudes-ayuda', [SolicitudAyudaApiController::class, 'index']);
Route::post('/solicitudes-ayuda', [SolicitudAyudaApiController::class, 'store']);
Route::patch('/solicitudes-ayuda/{id}/estado', [SolicitudAyudaApiController::class, 'actualizarEstado']);



Route::get('/chat-mensajes', [ChatMensajeApiController::class, 'index']);
Route::post('/chat-mensajes', [ChatMensajeApiController::class, 'store']);


// ==================== AUTENTICACIÓN ====================
Route::post('/usuarios/login', [AuthApiController::class, 'login']);

// ==================== USUARIOS ====================
Route::get('/usuarios', [UsuarioApiController::class, 'index']);
Route::get('/usuarios/{id}', [UsuarioApiController::class, 'show']);
Route::get('/usuarios/ci/{ci}', [UsuarioApiController::class, 'getByCi']);

// ==================== VOLUNTARIOS ====================
// Endpoints para la móvil (ruta /voluntario/voluntarios)
Route::prefix('voluntario')->group(function () {
    Route::get('/voluntarios', [VoluntarioApiController::class, 'index']);
    Route::get('/voluntarios/{id}', [VoluntarioApiController::class, 'show']);
});

// Endpoints adicionales de voluntarios
Route::get('/voluntarios', [VoluntarioApiController::class, 'index']);
Route::get('/voluntarios/{id}', [VoluntarioApiController::class, 'show']);
Route::post('/voluntarios', [VoluntarioApiController::class, 'store']);

// ==================== CAPACITACIONES Y CURSOS ====================
Route::get('/capacitaciones', [CapacitacionApiController::class, 'index']);
Route::get('/capacitaciones/{id}', [CapacitacionApiController::class, 'show']);
Route::post('/capacitaciones', [CapacitacionApiController::class, 'store']);
Route::post('/cursos', [CapacitacionApiController::class, 'storeCurso']);
Route::post('/etapas', [CapacitacionApiController::class, 'storeEtapa']);
Route::patch('/progreso/{id}/estado', [CapacitacionApiController::class, 'cambiarEstadoEtapa']);

// Cursos por voluntario (para la app móvil)
Route::get('/voluntarios/{id}/cursos', [CapacitacionApiController::class, 'getCursosByVoluntario']);
Route::post('/voluntarios/asignar-curso', [CapacitacionApiController::class, 'asignarCursoAVoluntario']);

// ==================== REPORTES POR VOLUNTARIO ====================
Route::get('/voluntarios/{id}/reportes', [ReporteApiController::class, 'getByVoluntario']);
Route::get('/voluntarios/{id}/reportes/ultimo', [ReporteApiController::class, 'getUltimoByVoluntario']);
Route::get('/voluntarios/{id}/necesidades', [ReporteApiController::class, 'getNecesidadesByVoluntario']);
Route::get('/voluntarios/{id}/capacitaciones-asignadas', [ReporteApiController::class, 'getCapacitacionesByVoluntario']);

// ==================== CONSULTAS ====================
Route::post('/consultas', [ConsultaApiController::class, 'store']);
Route::get('/consultas', [ConsultaApiController::class, 'index']);

// ==================== UNIVERSIDADES ====================
Route::apiResource('universidades', UniversidadApiController::class)->names([
    'index' => 'api.universidades.index',
    'store' => 'api.universidades.store',
    'show'  => 'api.universidades.show',
    'update'=> 'api.universidades.update',
    'destroy'=>'api.universidades.destroy',
]);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ==================== TRAZABILIDAD - API GATEWAY ====================
Route::get('/trazabilidad/{ci}', [TrazabilidadController::class, 'porVoluntario']);