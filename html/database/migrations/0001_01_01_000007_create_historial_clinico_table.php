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
    Schema::create('historial_clinico', function (Blueprint $table) {
        $table->id();

        $table->unsignedBigInteger('id_usuario')->unique();

        $table->timestamp('fecha_inicio')->useCurrent();
        $table->timestamp('fecha_actualizacion')->useCurrent();

        // foreign key correcta para tabla usuario (PK: id_usuario)
        $table->foreign('id_usuario')
              ->references('id_usuario')
              ->on('usuario')
              ->cascadeOnDelete()
              ->cascadeOnUpdate();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::dropIfExists('historial_clinico');
}

};
