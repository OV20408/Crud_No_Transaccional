<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reporte', function (Blueprint $table) {
            // Agregar columna id_historial
            $table->unsignedBigInteger('id_historial')->nullable()->after('id');
            
            // Agregar foreign key
            $table->foreign('id_historial')
                  ->references('id')
                  ->on('historial_clinico')
                  ->nullOnDelete()
                  ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::table('reporte', function (Blueprint $table) {
            $table->dropForeign(['id_historial']);
            $table->dropColumn('id_historial');
        });
    }
};