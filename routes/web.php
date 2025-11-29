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
use App\Models\Consulta;
use App\Events\ConsultaRespondida;


Route::post('/consultas/{id}/responder', function ($id) {
    $consulta = Consulta::findOrFail($id);

    $consulta->respuesta_admin = request('respuesta_admin');
    $consulta->estado = 'respondido';
    $consulta->save();

    // Emitimos evento para que la app vea la respuesta en tiempo real
    broadcast(new ConsultaRespondida($consulta))->toOthers();

    return redirect('/chat-consulta')->with('success', 'Respuesta enviada');
});




Route::get('/administradores', [AdministradorController::class, 'index'])
    ->name('administradores.index');

Route::get('/administradores/create', [AdministradorController::class, 'create'])
    ->name('administradores.create');

Route::post('/administradores', [AdministradorController::class, 'store'])
    ->name('administradores.store');

Route::post('/administradores/{id}/toggle-estado', [AdministradorController::class, 'toggleEstado'])
    ->name('administradores.toggle-estado');
    

    #----------------------------------------------------------

Route::get('/chat-consulta', function () {
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
});


#------------------------------------------------- HACIA ARRIBA ES LA COMS DE WEB A MOVIL






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

// Ruta GET adicional para logout (alternativa a POST)
Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout.get');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
