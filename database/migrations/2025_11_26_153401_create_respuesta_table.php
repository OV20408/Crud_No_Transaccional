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
        Schema::create('respuesta', function (Blueprint $table) {
    $table->id();
    $table->foreignId('id_evaluacion')
          ->constrained('evaluacion')
          ->cascadeOnDelete()
          ->cascadeOnUpdate();
    $table->string('texto_pregunta')->nullable();
    $table->string('respuesta_texto')->nullable();
    $table->timestamp('created_at')->useCurrent();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respuesta');
    }
};
