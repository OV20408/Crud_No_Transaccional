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
        Schema::create('aptitud_necesidades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_voluntario');
            $table->unsignedBigInteger('id_necesidad')->nullable();
            $table->unsignedBigInteger('id_reporte');
            $table->enum('nivel_aptitud', ['APTO_TODAS', 'APTO_ALGUNAS', 'NO_APTO'])->default('APTO_TODAS');
            $table->text('razon_ia')->nullable();
            $table->text('necesidades_recomendadas')->nullable(); // JSON con IDs de necesidades aptas
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();

            $table->foreign('id_voluntario')->references('id_usuario')->on('usuario')->onDelete('cascade');
            $table->foreign('id_necesidad')->references('id')->on('necesidad')->onDelete('cascade');
            $table->foreign('id_reporte')->references('id')->on('reporte')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aptitud_necesidades');
    }
};