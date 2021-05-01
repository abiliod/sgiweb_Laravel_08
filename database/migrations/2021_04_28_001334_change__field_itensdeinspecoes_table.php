<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFieldItensdeinspecoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('itensdeinspecoes', function (Blueprint $table) {

            DB::statement("ALTER TABLE `itensdeinspecoes` CHANGE `situacao` `situacao` ENUM('Em Inspeção','Inspecionado','Corroborado','Concluido','Não Respondido') default NULL;");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('itensdeinspecoes', function (Blueprint $table) {

//            situacao  enum('Em Inspeção','Inspecionado','Corroborado','Conferido','Pendente na Unidade','Pendente na Àrea')
//            DB::statement("ALTER TABLE itensdeinspecoes CHANGE `situacao` `situacao` ENUM('Em Inspeção','Inspecionado','Corroborado','Conferido','Pendente na Unidade','Pendente na Àrea') default NULL;");
        });
    }
}
