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
        Schema::table('consultas', function (Blueprint $table) {
            $table->string('respuesta_admin', 500)->nullable()->after('mensaje');
        });
    }

    public function down()
    {
        Schema::table('consultas', function (Blueprint $table) {
            $table->dropColumn('respuesta_admin');
        });
    }

};
