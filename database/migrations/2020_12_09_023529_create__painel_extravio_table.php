<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePainelExtravioTable extends Migration
{
   /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('painel_extravios', function (Blueprint $table) {
            $table->id();
            $table->string('objeto')->nullable();//pode ser nulo
            $table->date('data_evento')->nullable();//pode ser nulo
            $table->string('evento')->nullable();//pode ser nulo
            $table->string('trecho')->nullable();//pode ser nulo
            $table->string('evento_trecho')->nullable();//pode ser nulo
            $table->string('cliente')->nullable();//pode ser nulo
            $table->string('unid_origem')->nullable();//pode ser nulo
            $table->string('unid_destino')->nullable();//pode ser nulo
            $table->string('dr_origem')->nullable();//pode ser nulo
            $table->string('dr_destino')->nullable();//pode ser nulo
            $table->string('gestao_prealerta')->nullable();//pode ser nulo
            $table->string('automatico')->nullable();//pode ser nulo
            $table->string('manual')->nullable();//pode ser nulo
            $table->string('total')->nullable();//pode ser nulo
            $table->string('macroprocesso')->nullable();//pode ser nulo
            $table->string('postado')->nullable();//pode ser nulo
            $table->string('ultimo_evento_extraviado')->nullable();//pode ser nulo
            $table->string('ultimo_evento_em_transito')->nullable();//pode ser nulo
            $table->string('ultimo_evento')->nullable();//pode ser nulo
            $table->date('ultimo_evento_data')->nullable();//pode ser nulo
            $table->string('evento_finalizador')->nullable();//pode ser nulo
            $table->string('tipo')->nullable();//pode ser nulo
            $table->string('analise_sro')->nullable();//pode ser nulo
            $table->string('unid_origem_apelido')->nullable();//pode ser nulo
            $table->string('unid_destino_apelido')->nullable();//pode ser nulo
            $table->string('trecho_real')->nullable();//pode ser nulo
            $table->string('se_postagem')->nullable();//pode ser nulo
            $table->string('unidade_postagem')->nullable();//pode ser nulo
            $table->date('data_postagem')->nullable();//pode ser nulo
            $table->string('familia')->nullable();//pode ser nulo
            $table->string('ultimo_evento_sinistro')->nullable();//pode ser nulo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('painel_extravios');
    }
}
