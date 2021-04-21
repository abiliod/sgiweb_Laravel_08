<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProtersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proters', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_de_pendencia')->nullable();
            $table->string('divergencia_peso')->nullable();
            $table->string('divergencia_cep')->nullable();
            $table->string('se')->nullable();
            $table->string('tipo_de_unidade')->nullable();
            $table->string('mcu')->nullable();
            $table->date('data_da_pendencia')->nullable();
            $table->string('status_da_pendencia')->nullable();
            $table->string('status_da_unidade')->nullable();
            $table->string('nome_da_unidade')->nullable();
            $table->string('tipo_de_atendimento')->nullable();
            $table->string('matricula_atendente')->nullable();
            $table->string('no_do_objeto')->nullable();
            $table->date('data_da_postagem')->nullable();
            $table->date('data_da_entrega')->nullable();
            $table->string('codigo_do_servico')->nullable();
            $table->string('cep_contabilizado_sara')->nullable();
            $table->string('cep_entrega_sro')->nullable();
            $table->string('peso_tarifado_financeiro')->nullable();
            $table->string('comprimento_financeiro')->nullable();
            $table->string('largura_financeiro')->nullable();
            $table->string('largura_contab_sara')->nullable();
            $table->string('altura_financeiro')->nullable();
            $table->string('peso_cubico_financeiro')->nullable();
            $table->string('peso_real_mectri')->nullable();
            $table->string('comprimento_mectri')->nullable();
            $table->decimal('valor_tarifado_financeiro', 8, 2)->default(0);
            $table->decimal('valor_tarifado_mectri', 8, 2)->default(0);
            $table->decimal('diferenca_a_recolher', 8, 2)->default(0);
            $table->string('largura_mectri')->nullable();
            $table->string('altura_mectri')->nullable();
            $table->string('peso_cubico_mectri')->nullable();
            $table->string('peso_tarifado_mectri')->nullable();
            $table->string('cnpj_do_cliente')->nullable();
            $table->string('contrato')->nullable();
            $table->string('cartao_postagem')->nullable();
            $table->string('nome_do_cliente')->nullable();
            $table->string('qtd_duplicidades')->nullable();
            $table->mediumText('ultima_manifestacao')->nullable();
            $table->date('data_ultima_manifestacao')->nullable();
            $table->string('mcu_triagem')->nullable();
            $table->string('centro')->nullable();
            $table->string('peso')->nullable();
            $table->string('volume')->nullable();
            $table->string('altura')->nullable();
            $table->string('largura')->nullable();
            $table->string('comprimento')->nullable();
            $table->date('data_de_leitura')->nullable();
            $table->string('tipo_do_objeto')->nullable();
            $table->string('cep_destino')->nullable();
            $table->string('tipo_de_inducao')->nullable();
            $table->string('numero_da_maquina')->nullable();
            $table->string('codigo_da_estacao')->nullable();
            $table->string('origem_pendencia')->nullable();
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
        Schema::dropIfExists('proters');
    }
}
