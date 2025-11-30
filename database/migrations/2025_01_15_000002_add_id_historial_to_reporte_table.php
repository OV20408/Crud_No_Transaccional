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
        Schema::table('reporte', function (Blueprint $table) {
            $table->unsignedBigInteger('id_historial')->nullable()->after('id');
            
            $table->foreign('id_historial')
                  ->references('id')
                  ->on('historial_clinico')
                  ->nullOnDelete()
                  ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reporte', function (Blueprint $table) {
            $table->dropForeign(['id_historial']);
            $table->dropColumn('id_historial');
        });
    }
};
