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
        Schema::create('pregunta', function (Blueprint $table) {
            $table->id();
            $table->string('texto');
            $table->string('tipo')->nullable();
            $table->foreignId('id_test')
                ->constrained('test')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->integer('orden')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pregunta');
    }
};
