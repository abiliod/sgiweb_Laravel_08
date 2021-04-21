<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApontamentoCVSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apontamento_c_v_s', function (Blueprint $table) {
            $table->id();
            $table->string('dr_detentora')->nullable();//pode ser nulo
            $table->string('unidade_detentora')->nullable();//pode ser nulo
            $table->string('origem_destino')->nullable();//pode ser nulo
            $table->string('tipo_linha')->nullable();//pode ser nulo
            $table->string('numero_da_linha')->nullable();//pode ser nulo
            $table->string('controle_viagem')->nullable();//pode ser nulo
            $table->string('numero_ficha_tec')->nullable();//pode ser nulo
            $table->string('sentido')->nullable();//pode ser nulo
            $table->string('status')->nullable();//pode ser nulo
            $table->string('sequencia_do_cv')->nullable();//pode ser nulo
            $table->string('ponto_parada')->nullable();//pode ser nulo
            $table->string('data_chegada')->nullable();//pode ser nulo
            $table->string('descricao_ponto_parada')->nullable();//pode ser nulo
            $table->string('drac_ponto_de_parada')->nullable();//pode ser nulo
            $table->string('hodometro_chegada')->nullable();//pode ser nulo
            $table->string('hodometro_previsto_do_trecho')->nullable();//pode ser nulo
            $table->string('hodometro_partida')->nullable();//pode ser nulo
            $table->string('codigo_ocorrencia')->nullable();//pode ser nulo
            $table->string('descricao_ocorrencia')->nullable();//pode ser nulo
            $table->string('codigo_transportador')->nullable();//pode ser nulo
            $table->string('descricao_transportador')->nullable();//pode ser nulo
            $table->string('no_seq_contrato')->nullable();//pode ser nulo
            $table->string('item_do_contrato')->nullable();//pode ser nulo
            $table->string('placa_veiculo')->nullable();//pode ser nulo
            $table->string('placa_carreta')->nullable();//pode ser nulo
            $table->date('inicio_viagem')->nullable();//pode ser nulo
            $table->time('hora_chegada')->nullable();//pode ser nulo
            $table->date('data_chegada_prevista')->nullable();//pode ser nulo
            $table->time('horario_chegada_previsto')->nullable();//pode ser nulo
            $table->date('data_partida')->nullable();//pode ser nulo
            $table->time('hora_partida')->nullable();//pode ser nulo
            $table->date('data_partida_prevista')->nullable();//pode ser nulo
            $table->time('horario_partida_previsto')->nullable();//pode ser nulo
            $table->date('data_conclusao')->nullable();//pode ser nulo
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
        Schema::dropIfExists('apontamento_c_v_s');
    }
}
