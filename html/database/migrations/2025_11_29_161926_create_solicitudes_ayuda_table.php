<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('solicitudes_ayuda', function (Blueprint $table) {
            $table->id();

            // FK al voluntario que pide ayuda (usuario.id_usuario)
            $table->unsignedBigInteger('voluntario_id');



            // tipo: Físico / Emocional / Recursos
            $table->string('tipo', 50);

            // nivelEmergencia: Bajo / Medio / Alto
            $table->string('nivel_emergencia', 20);

            // Descripción libre de la emergencia
            $table->string('descripcion', 500);

            // Geolocalización
            $table->decimal('latitud', 10, 7);
            $table->decimal('longitud', 10, 7);

            // Estado de la solicitud: sin responder / en progreso / respondido / resuelto
            $table->string('estado', 20)->default('sin responder');

            // CIs de voluntarios que irán a ayudar (lista separada por coma, opcional)
            $table->string('ci_voluntarios_acudir')->nullable();

            // Cuándo se marcó como respondida / resuelta
            $table->timestamp('fecha_respondida')->nullable();

            $table->timestamps();

            $table->foreign('voluntario_id')
                ->references('id_usuario')
                ->on('usuario')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_ayuda');
    }


};
