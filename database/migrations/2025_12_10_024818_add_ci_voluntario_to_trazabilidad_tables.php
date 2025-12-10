<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Agregar campo ci_voluntario_accion para trazabilidad del API Gateway
     * Este campo almacena el CI del usuario que realizó la acción (NO como FK)
     */
    public function up(): void
    {
        // Evaluaciones - cuando se completa una evaluación
        Schema::table('evaluacion', function (Blueprint $table) {
            $table->string('ci_voluntario_accion')->nullable()->after('id_universidad');
        });

        // Respuestas - cuando se responde una pregunta
        Schema::table('respuesta', function (Blueprint $table) {
            $table->string('ci_voluntario_accion')->nullable()->after('id_pregunta');
        });

        // Reportes - cuando se genera un reporte
        Schema::table('reporte', function (Blueprint $table) {
            $table->string('ci_voluntario_accion')->nullable()->after('id_historial');
        });

        // Progreso Voluntario - cuando avanza en capacitaciones
        Schema::table('progreso_voluntario', function (Blueprint $table) {
            $table->string('ci_voluntario_accion')->nullable()->after('id_etapa');
        });

        // Consultas - cuando se crea una consulta
        Schema::table('consultas', function (Blueprint $table) {
            $table->string('ci_voluntario_accion')->nullable()->after('estado');
        });

        // Chat Mensajes - cuando se envía un mensaje
        Schema::table('chat_mensajes', function (Blueprint $table) {
            $table->string('ci_voluntario_accion')->nullable()->after('leido_en');
        });

        // Solicitudes de Ayuda - cuando se crea una solicitud
        Schema::table('solicitudes_ayuda', function (Blueprint $table) {
            $table->string('ci_voluntario_accion')->nullable()->after('fecha_respondida');
        });

        // Curso Recomendaciones - cuando se asigna/acepta una recomendación
        Schema::table('curso_recomendaciones', function (Blueprint $table) {
            $table->string('ci_voluntario_accion')->nullable()->after('estado');
        });

        // Aptitud Necesidades - cuando se evalúa aptitud
        Schema::table('aptitud_necesidades', function (Blueprint $table) {
            $table->string('ci_voluntario_accion')->nullable()->after('estado');
        });

        // Historial Clínico - cuando se crea/modifica historial
        Schema::table('historial_clinico', function (Blueprint $table) {
            $table->string('ci_voluntario_accion')->nullable()->after('fecha_actualizacion');
        });

        // Reporte-Necesidad - cuando se asigna una necesidad a un reporte
        Schema::table('reporte_necesidad', function (Blueprint $table) {
            $table->string('ci_voluntario_accion')->nullable();
        });

        // Reporte-Progreso Voluntario - relación de progreso con reportes
        Schema::table('reporte_progreso_voluntario', function (Blueprint $table) {
            $table->string('ci_voluntario_accion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluacion', function (Blueprint $table) {
            $table->dropColumn('ci_voluntario_accion');
        });

        Schema::table('respuesta', function (Blueprint $table) {
            $table->dropColumn('ci_voluntario_accion');
        });

        Schema::table('reporte', function (Blueprint $table) {
            $table->dropColumn('ci_voluntario_accion');
        });

        Schema::table('progreso_voluntario', function (Blueprint $table) {
            $table->dropColumn('ci_voluntario_accion');
        });

        Schema::table('consultas', function (Blueprint $table) {
            $table->dropColumn('ci_voluntario_accion');
        });

        Schema::table('chat_mensajes', function (Blueprint $table) {
            $table->dropColumn('ci_voluntario_accion');
        });

        Schema::table('solicitudes_ayuda', function (Blueprint $table) {
            $table->dropColumn('ci_voluntario_accion');
        });

        Schema::table('curso_recomendaciones', function (Blueprint $table) {
            $table->dropColumn('ci_voluntario_accion');
        });

        Schema::table('aptitud_necesidades', function (Blueprint $table) {
            $table->dropColumn('ci_voluntario_accion');
        });

        Schema::table('historial_clinico', function (Blueprint $table) {
            $table->dropColumn('ci_voluntario_accion');
        });

        Schema::table('reporte_necesidad', function (Blueprint $table) {
            $table->dropColumn('ci_voluntario_accion');
        });

        Schema::table('reporte_progreso_voluntario', function (Blueprint $table) {
            $table->dropColumn('ci_voluntario_accion');
        });
    }
};
