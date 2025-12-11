<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('usuario', function (Blueprint $table) {
            $table->timestamp('fecha_inactivacion')->nullable()->after('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('usuario', function (Blueprint $table) {
            $table->dropColumn('fecha_inactivacion');
        });
    }
};
