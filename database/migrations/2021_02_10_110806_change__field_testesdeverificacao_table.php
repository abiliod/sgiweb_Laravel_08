<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeFieldTestesdeverificacaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //  Basta criar uma nova migração e adicionar os itens antigos + os novos itens.
//        Schema::table('testesdeverificacao', function (Blueprint $table) {
//            $table->enum('preVerificar',['Sim', 'Não', 'Gerar_Conteúdo']);
//        });

        DB::statement("ALTER TABLE `testesdeverificacao` CHANGE `preVerificar` `preVerificar` ENUM('Sim', 'Não', 'Gerar_Conteúdo') default NULL;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('testesdeverificacao', function (Blueprint $table) {
            //
        });
    }
}
