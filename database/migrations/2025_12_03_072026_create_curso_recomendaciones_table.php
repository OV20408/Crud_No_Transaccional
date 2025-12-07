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
        Schema::create('curso_recomendaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_voluntario')
                ->constrained('usuario', 'id_usuario')
                ->cascadeOnDelete();
            $table->foreignId('id_curso')
                ->nullable()
                ->constrained('curso')
                ->nullOnDelete();
            $table->foreignId('id_reporte')
                ->nullable()
                ->constrained('reporte')
                ->nullOnDelete();
            $table->text('mensaje_ia'); // Mensaje generado por la IA
            $table->text('razon')->nullable(); // Razón de la recomendación
            $table->enum('estado', ['pendiente', 'vista', 'aplicada'])->default('pendiente');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curso_recomendaciones');
    }
};