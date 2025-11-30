<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reporte_progreso_voluntario', function (Blueprint $table) {
    $table->foreignId('id_reporte')
          ->constrained('reporte')
          ->cascadeOnDelete()
          ->cascadeOnUpdate();

    $table->foreignId('id_progreso')
          ->constrained('progreso_voluntario')
          ->cascadeOnDelete()
          ->cascadeOnUpdate();

    $table->primary(['id_reporte', 'id_progreso']);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reporte_progreso_voluntario');
    }
};
