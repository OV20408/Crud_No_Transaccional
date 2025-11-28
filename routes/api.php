<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UniversidadApiController;
use App\Http\Controllers\Api\ConsultaApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\VoluntarioApiController;
use App\Http\Controllers\Api\UsuarioApiController;

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

// ==================== SANCTUM ====================
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
