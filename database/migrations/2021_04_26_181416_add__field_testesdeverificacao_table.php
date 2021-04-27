<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldTestesdeverificacaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('testesdeverificacao', function (Blueprint $table) {
            $table->decimal('maximodepontos', 4, 2)->default(0)->after('totalPontos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('testesdeverificacao', function (Blueprint $table) {
            $table->dropColumn('maximodepontos');
        });
    }
}
