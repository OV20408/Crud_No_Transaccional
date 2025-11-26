<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('consultas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('voluntario_id');
            $table->unsignedBigInteger('necesidad_id')->nullable();

            $table->string('mensaje', 500);
            $table->string('estado', 20)->default('pendiente');
            $table->timestamps();

            // FK hacia tabla USERS de Laravel
            $table->foreign('voluntario_id')
            ->references('id_usuario')->on('usuario')
            ->onDelete('cascade');


            // FK hacia necesidad
            $table->foreign('necesidad_id')
                ->references('id')->on('necesidad')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultas');
    }
};
