<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsInspecoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inspecoes', function (Blueprint $table) {
            $table->decimal('totalpontosInspetor', 6, 2)->default(0)->after('totalPontos')->nullable();
            $table->decimal('pontuacaoFinal', 6, 2)->default(0)->after('totalpontosInspetor')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inspecoes', function (Blueprint $table) {
            $table->dropColumn('totalpontosInspetor');
            $table->dropColumn('pontuacaoFinal');
        });
    }
}
