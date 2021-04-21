<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateControleDeViagemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('controle_de_viagens', function (Blueprint $table) {
            $table->id();
            $table->string('dr_detentora')->nullable();//pode ser nulo
            $table->string('unidade_detentora')->nullable();//pode ser nulo
            $table->string('origem_destino')->nullable();//pode ser nulo
            $table->string('tipo_linha')->nullable();//pode ser nulo
            $table->string('numero_da_linha')->nullable();
            $table->bigInteger('controle_viagem')->nullable();
            $table->bigInteger('numero_ficha_tec')->nullable();
            $table->string('sentido')->nullable();
            $table->string('status')->nullable();
            $table->string('sequencia_do_cv')->nullable();
            $table->bigInteger('ponto_parada')->nullable();
            $table->string('descricao_ponto_parada')->nullable();
            $table->string('drac_ponto_de_parada')->nullable();
            $table->string('tipo_de_operacao')->nullable();
            $table->bigInteger('quantidade')->nullable();
            $table->bigInteger('peso')->nullable();
            $table->string('unitizador')->nullable();
            $table->string('tipo_de_servico')->nullable();
            $table->string('descricao_do_servico')->nullable();
            $table->bigInteger('codigo_de_destino')->nullable();
            $table->string('local_de_destino')->nullable();
            $table->date('inicio_viagem')->nullable();
            $table->date('data_chegada_prevista')->nullable();
            $table->date('data_partida_prevista')->nullable();
            $table->time('horario_chegada_prevista')->nullable();
            $table->time('horario_partida_prevista')->nullable();
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
        Schema::dropIfExists('controle_de_viagens');
    }
}
