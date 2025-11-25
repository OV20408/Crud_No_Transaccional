<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UniversidadApiController;


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
