<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSnciTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('snci', function (Blueprint $table) {

            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
            $table->autocommit = 0;
            $table->CHECKSUM = 1;
            /*criando colunas*/
            $table->id();
            $table->string('sto')->nullable();
            $table->string('modalidade')->nullable();
            $table->string('diretoria')->nullable();
            $table->string('codigo_unidade')->nullable();
            $table->string('descricao_da_unidade')->nullable();
            $table->string('no_inspecao')->nullable();
            $table->string('no_grupo')->nullable();
            $table->string('descricao_do_grupo')->nullable();
            $table->string('no_item')->nullable();
            $table->text('descricao_item')->nullable();
            $table->string('codigo_reate')->nullable();
            $table->string('ano')->nullable();
            $table->string('resposta')->nullable();
            $table->text('comentario')->nullable();
            $table->string('valor')->nullable();
            $table->string('caracteresvlr')->nullable();
            $table->decimal('falta', 8, 2)->default(0);
            $table->decimal('sobra', 8, 2)->default(0);
            $table->decimal('emrisco', 8, 2)->default(0);
            $table->date('dtultatu')->nullable();
            $table->string('nome_do_usuario')->nullable();
            $table->string('recomendacao')->nullable();
            $table->string('hora_pre_inspecao')->nullable();
            $table->date('dt_inic_desloc')->nullable();
            $table->string('hora_desloc')->nullable();
            $table->date('dt_fim_desloc')->nullable();
            $table->date('dt_inic_inspecao')->nullable();
            $table->date('dt_fim_inspecao')->nullable();
            $table->string('hora_inspecao')->nullable();
            $table->string('situacao')->nullable();
            $table->date('dt_encerram')->nullable();
            $table->string('coordenador')->nullable();
            $table->string('responsavel')->nullable();
            $table->string('motivo')->nullable();
            $table->string('status')->nullable();
            $table->string('sigla_do_status')->nullable();
            $table->string('descricao_do_status')->nullable();
            $table->date('dt_posicao')->nullable();
            $table->date('data_previsao_solucao')->nullable();
            $table->string('area')->nullable();
            $table->string('nome_da_area')->nullable();
            $table->text('parecer')->nullable();
            $table->decimal('valor_recuperado', 8, 2)->default(0);
            $table->string('processo')->nullable();
            $table->string('tipo_processo')->nullable();
            $table->string('sei')->nullable();
            $table->string('ncisei')->nullable();
            $table->string('reinc_relat')->nullable();
            $table->string('reinc_grupo')->nullable();
            $table->string('reinc_item')->nullable();
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
        Schema::dropIfExists('snci');
    }
}
