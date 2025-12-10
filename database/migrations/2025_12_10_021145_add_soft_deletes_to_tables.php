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
        // Tablas principales
        Schema::table('usuario', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('reporte', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('necesidad', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('historial_clinico', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('capacitacion', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('curso', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('pregunta', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('etapa', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('test', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('universidad', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('progreso_voluntario', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('evaluacion', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('respuesta', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('consultas', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('chat_mensajes', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('solicitudes_ayuda', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('curso_recomendaciones', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('aptitud_necesidades', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuario', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('reporte', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('necesidad', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('historial_clinico', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('capacitacion', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('curso', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('pregunta', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('etapa', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('test', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('universidad', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('progreso_voluntario', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('evaluacion', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('respuesta', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('consultas', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('chat_mensajes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('solicitudes_ayuda', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('curso_recomendaciones', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('aptitud_necesidades', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
