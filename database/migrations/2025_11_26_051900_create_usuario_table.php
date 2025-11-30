<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('usuario', function (Blueprint $table) {

            $table->id('id_usuario'); // PRIMARY KEY REAL

            $table->string('nombres');
            $table->string('apellidos');
            $table->string('ci')->unique();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('genero', 50)->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('direccion_domicilio')->nullable();
            $table->string('contrasena');
            $table->string('estado', 50)->default('activo');

            // Rol asociado
            $table->unsignedBigInteger('id_rol')->nullable();
            $table->foreign('id_rol')
              ->references('id')
              ->on('rol')
              ->nullOnDelete()
              ->cascadeOnUpdate();

            $table->string('nivel_entrenamiento')->nullable();
            $table->string('entidad_pertenencia')->nullable();
            $table->string('tipo_sangre', 10)->nullable();
            $table->string('foto_ci')->nullable();
            $table->string('licencia_conducir')->nullable();
            $table->string('foto_licencia')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuario');
    }
};
