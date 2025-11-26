<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UniversidadApiController;
use App\Http\Controllers\Api\ConsultaApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\VoluntarioApiController;
use App\Http\Controllers\Api\UsuarioApiController;


Route::get('/usuarios', [UsuarioApiController::class, 'index']);
Route::get('/usuarios/{id}', [UsuarioApiController::class, 'show']);
Route::get('/usuarios/ci/{ci}', [UsuarioApiController::class, 'getByCi']);

Route::get('/voluntario/voluntarios', [VoluntarioApiController::class, 'index']);
Route::get('/voluntario/voluntarios/{id}', [VoluntarioApiController::class, 'show']);




Route::post('/usuarios/login', [AuthApiController::class, 'login']);




Route::post('/consultas', [ConsultaApiController::class, 'store']);
Route::get('/consultas', [ConsultaApiController::class, 'index']);



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
