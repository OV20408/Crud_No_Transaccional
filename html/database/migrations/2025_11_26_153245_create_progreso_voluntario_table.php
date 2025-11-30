<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('progreso_voluntario', function (Blueprint $table) {
        $table->id();

        $table->unsignedBigInteger('id_usuario');
        $table->unsignedBigInteger('id_etapa');

        $table->string('estado')->default('en_progreso');
        $table->timestamp('fecha_inicio')->useCurrent();
        $table->timestamp('fecha_finalizacion')->nullable();

        // foreign keys correctas
        $table->foreign('id_usuario')
              ->references('id_usuario')
              ->on('usuario')
              ->cascadeOnDelete()
              ->cascadeOnUpdate();

        $table->foreign('id_etapa')
              ->references('id')
              ->on('etapa')
              ->cascadeOnDelete()
              ->cascadeOnUpdate();

        $table->unique(['id_usuario', 'id_etapa']);
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progreso_voluntario');
    }
};
