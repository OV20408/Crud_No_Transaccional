<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_mensajes', function (Blueprint $table) {
            $table->id();

            // Voluntario / usuario participante del chat
            $table->unsignedBigInteger('voluntario_id');

            // Quién envía el mensaje
            $table->enum('de', ['voluntario', 'admin']);

            $table->text('texto');

            // opcional: para “visto”
            $table->timestamp('leido_en')->nullable();

            $table->timestamps();

            // FK a tu tabla usuario (PK id_usuario)
            $table->foreign('voluntario_id')
                  ->references('id_usuario')
                  ->on('usuario')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->index(['voluntario_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_mensajes');
    }
};
