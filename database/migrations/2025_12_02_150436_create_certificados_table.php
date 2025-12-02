<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('certificados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_capacitacion');
            $table->string('codigo_certificado')->unique(); // Ej: CERT-2025-001
            $table->date('fecha_emision');
            $table->string('archivo_pdf')->nullable(); // Ruta del PDF generado
            $table->boolean('enviado_email')->default(false);
            $table->timestamp('fecha_envio_email')->nullable();
            $table->enum('estado', ['activo', 'revocado'])->default('activo');
            $table->timestamps();
            
            $table->index(['id_usuario', 'id_capacitacion']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('certificados');
    }
};