<?php

use Illuminate\Support\Facades\Route;
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
// Lista y creación real de administradores
Route::resource('administradores', AdministradorController::class)
    ->only(['index', 'create', 'store']);

// Cambiar estado Activo/Inactivo
Route::patch('administradores/{id}/toggle-estado', [AdministradorController::class, 'toggleEstado'])
    ->name('administradores.toggle-estado');



Route::get('/chat-consulta', function () {
    $consultas = DB::table('consultas')
        ->join('usuario', 'usuario.id_usuario', '=', 'consultas.voluntario_id')
        ->select('consultas.*', 'usuario.nombres', 'usuario.apellidos', 'usuario.ci')
        ->orderBy('consultas.id', 'DESC')
        ->get();

    return view('chat-consulta.index', compact('consultas'));
});


Route::post('/consultas/{id}/responder', function ($id) {
    DB::table('consultas')
        ->where('id', $id)
        ->update([
            'respuesta_admin' => request('respuesta_admin'),
            'estado' => 'respondido'
        ]);

    return redirect('/chat-consulta')->with('success', 'Respuesta enviada');
});


Route::view('/ayudas_solicitadas', 'ayudas_solicitadas.index')->name('ayudas_solicitadas.index');



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



Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
