<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reporte', function (Blueprint $table) {
            $table->text('respuestas_fisico')->nullable()->after('resumen_fisico');
            $table->text('respuestas_emocional')->nullable()->after('resumen_emocional');
        });
    }

    public function down(): void
    {
        Schema::table('reporte', function (Blueprint $table) {
            $table->dropColumn(['respuestas_fisico', 'respuestas_emocional']);
        });
    }
};
