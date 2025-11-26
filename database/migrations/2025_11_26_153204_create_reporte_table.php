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
        Schema::create('reporte', function (Blueprint $table) {
            $table->id();
            $table->string('estado_general')->nullable();
            $table->timestamp('fecha_generado')->useCurrent();
            $table->string('observaciones', 2000)->nullable();
            $table->string('recomendaciones')->nullable();
            $table->string('resumen_emocional', 1000)->nullable();
            $table->string('resumen_fisico', 1000)->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reporte');
    }
};
