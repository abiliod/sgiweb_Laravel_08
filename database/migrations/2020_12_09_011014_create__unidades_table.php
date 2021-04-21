<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnidadesTable extends Migration
{
        /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('unidades', function (Blueprint $table) {
            $table->id();
            $table->biginteger('tipoUnidade_id')->unsigned();
            $table->string('se');
            $table->string('seDescricao');
            $table->string('mcu');
            $table->biginteger('an8');
            $table->string('sto');
            $table->string('status_unidadeDesc');
            $table->string('status_unidade');
            $table->string('descricao');
            $table->string('tipoOrgaoCod')->nullable();
            $table->string('tipoOrgaoDesc')->nullable();
            $table->string('cnpj')->nullable();
            $table->string('categoria')->nullable();
            $table->string('mecanizacao')->nullable();
            $table->string('faixaCepIni')->nullable();
            $table->string('faixaCepFim')->nullable();
            $table->string('tem_distribuicao')->nullable();
            $table->string('tipoEstrutura')->nullable();
            $table->string('quantidade_guiches')->nullable();
            $table->string('guiches_ocupados')->nullable();
            $table->string('ddd')->nullable();
            $table->string('telefone')->nullable();
            $table->string('mcu_subordinacaoAdm')->nullable();
            $table->string('desc_subordinacaoAdm')->nullable();
            $table->string('nomeResponsavelUnidade')->nullable();
            $table->string('documentRespUnidade')->nullable();
            $table->string('email')->nullable();
            $table->string('tipo_de_estrutura')->nullable();
            $table->string('subordinacao_tecnica')->nullable();
            $table->time('inicio_expediente')->nullable();
            $table->time('final_expediente')->nullable();
            $table->time('inicio_intervalo_refeicao')->nullable();
            $table->time('final_intervalo_refeicao')->nullable();
            $table->string('trabalha_sabado')->nullable();
            $table->time('inicio_expediente_sabado')->nullable();
            $table->time('final_expediente_sabado')->nullable();
            $table->string('trabalha_domingo')->nullable();
            $table->time('inicio_expediente_domingo')->nullable();
            $table->time('final_expediente_domingo')->nullable();
            $table->string('tem_plantao')->nullable();
            $table->time('inicio_plantao_sabado')->nullable();
            $table->time('final_plantao_sabado')->nullable();
            $table->time('inicio_plantao_domingo')->nullable();
            $table->time('final_plantao_domingo')->nullable();
            $table->time('inicio_distribuicao')->nullable();
            $table->time('final_distribuicao')->nullable();
            $table->time('horario_lim_post_na_semana')->nullable();
            $table->time('horario_lim_post_final_semana')->nullable();
            $table->timestamps();
        });

        Schema::table('unidades', function (Blueprint $table) {

            $table->index('mcu');
         //   $table->index('tipoUnidade_id');
            $table->foreign('tipoUnidade_id')
                ->references('id')
                ->on('tiposdeunidade')
                ->onDelete('cascade');
       });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
            Schema::dropIfExists('unidades');
    }
}
