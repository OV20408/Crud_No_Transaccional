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
        Schema::create('evaluacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_reporte')
                ->constrained('reporte')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('id_test')
                ->constrained('test')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('id_universidad')
                ->nullable()
                ->constrained('universidad')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->timestamp('fecha')->useCurrent();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluacion');
    }
};
