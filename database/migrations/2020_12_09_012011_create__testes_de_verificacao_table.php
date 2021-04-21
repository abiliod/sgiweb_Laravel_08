<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestesDeVerificacaoTable extends Migration
{
        /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('testesDeVerificacao', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('grupoVerificacao_id')->unsigned();
            $table->integer('numeroDoTeste');
            $table->mediumText('teste');
            $table->text('ajuda')->nullable();
            $table->text('amostra')->nullable();
            $table->text('sappp')->nullable();
            $table->mediumText('norma')->nullable();
            $table->mediumText('tabela_CFP')->nullable();
            $table->integer('impactoFinanceiro')->default(0);
            $table->integer('riscoFinanceiro')->default(0);
            $table->integer('descumprimentoLeisContratos')->default(0);
            $table->integer('descumprimentoNormaInterna')->default(0);
            $table->integer('riscoSegurancaIntegridade')->default(0);
            $table->integer('riscoImgInstitucional')->default(0);
            $table->integer('inspecaoObrigatoria')->default(0);
            $table->integer('totalPontos')->default(0);
            $table->text('roteiroConforme')->nullable();
            $table->text('roteiroNaoConforme')->nullable();
            $table->mediumText('roteiroNaoVerificado')->nullable();
            $table->mediumText('itemanosanteriores')->nullable();
            $table->mediumText('consequencias')->nullable();
            $table->mediumText('orientacao')->nullable();
            $table->enum('preVerificar', ['Sim', 'Não'])->default('Não');
            $table->timestamps();
        });




        Schema::table('testesDeVerificacao', function (Blueprint $table) {

            $table->unique(['grupoVerificacao_id', 'numeroDoTeste']);

               $table->foreign('grupoVerificacao_id')
                     ->references('id')
                     ->on('gruposDeVerificacao')
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

        Schema::dropIfExists('testesDeVerificacao');
    }
}
