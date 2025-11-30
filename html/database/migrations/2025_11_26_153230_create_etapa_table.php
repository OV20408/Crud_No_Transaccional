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
        Schema::create('etapa', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('orden');
            $table->foreignId('id_curso')
                ->constrained('curso')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('descripcion')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etapa');
    }
};
