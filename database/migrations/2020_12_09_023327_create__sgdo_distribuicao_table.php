<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSgdoDistribuicaoTable extends Migration
{
   /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sgdo_distribuicao', function (Blueprint $table) {
            $table->id();
            $table->string('dr')->nullable();//pode ser nulo
            $table->string('unidade')->nullable();//pode ser nulo
            $table->string('mcu')->nullable();//pode ser nulo
            $table->string('centralizadora')->nullable();//pode ser nulo
            $table->string('mcu_centralizadora')->nullable();//pode ser nulo
            $table->string('distrito')->nullable();//pode ser nulo
            $table->string('area')->nullable();//pode ser nulo
            $table->string('locomocao')->nullable();//pode ser nulo
            $table->string('funcionario')->nullable();//pode ser nulo
            $table->string('matricula')->nullable();//pode ser nulo
            $table->date('data_incio_atividade')->nullable();//pode ser nulo
            $table->time('hora_incio_atividade')->nullable();//pode ser nulo
            $table->date('data_saida')->nullable();//pode ser nulo
            $table->time('hora_saida')->nullable();//pode ser nulo
            $table->date('data_retorno')->nullable();//pode ser nulo
            $table->time('hora_retorno')->nullable();//pode ser nulo
            $table->date('data_tpc')->nullable();//pode ser nulo
            $table->time('hora_do_tpc')->nullable();//pode ser nulo
            $table->date('data_termino_atividade')->nullable();//pode ser nulo
            $table->time('hora_termino_atividade')->nullable();//pode ser nulo

            $table->string('justificado')->nullable();//pode ser nulo
            $table->string('peso_da_bolsa_kg')->nullable();//pode ser nulo
            $table->string('peso_do_da_kg')->nullable();//pode ser nulo
            $table->string('quantidade_de_da')->nullable();//pode ser nulo
            $table->string('quantidade_de_gu')->nullable();//pode ser nulo
            $table->string('quantidade_de_objetos_qualificados')->nullable();//pode ser nulo
            $table->string('quantidade_de_objetos_coletados')->nullable();//pode ser nulo
            $table->string('quantidade_de_pontos_de_entregacoleta')->nullable();//pode ser nulo
            $table->string('quilometragem_percorrida')->nullable();//pode ser nulo
            $table->string('residuo_simples')->nullable();//pode ser nulo
            $table->string('residuo_qualificado')->nullable();//pode ser nulo
            $table->string('almoca_na_unidade')->nullable();//pode ser nulo
            $table->string('compartilhado')->nullable();//pode ser nulo
            $table->string('tipo_de_distrito')->nullable();//pode ser nulo

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
        Schema::dropIfExists('sgdo_distribuicao');
    }
}
