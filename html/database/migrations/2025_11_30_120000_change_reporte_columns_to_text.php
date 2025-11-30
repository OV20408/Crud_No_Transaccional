<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reporte', function (Blueprint $table) {
            $table->text('resumen_fisico')->nullable()->change();
            $table->text('resumen_emocional')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('reporte', function (Blueprint $table) {
            $table->string('resumen_fisico', 1000)->nullable()->change();
            $table->string('resumen_emocional', 1000)->nullable()->change();
        });
    }
};
