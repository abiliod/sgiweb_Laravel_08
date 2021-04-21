<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItensdeinspecoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('itensdeinspecoes', function (Blueprint $table) {
            $table->id();
            $table->biginteger('inspecao_id')->unsigned(); //veriricação relacionada
            $table->biginteger('unidade_id')->unsigned(); //unidade verificada
            $table->biginteger('tipoUnidade_id')->unsigned(); //Tipo de unidade
            $table->biginteger('grupoVerificacao_id')->unsigned();  //grupo de verificação
            $table->biginteger('testeVerificacao_id')->unsigned(); // $registro->id teste de verificação
            $table->enum('avaliacao', ['Conforme', 'Não Conforme', 'Não Executa Tarefa', 'Não Verificado'])->default('Conforme');
            $table->text('oportunidadeAprimoramento')->nullable();
            $table->string('diretorio')->nullable();//pode ser nulo
            $table->mediumText('imagem')->nullable();// campo DTO  pode ser nulo
            $table->text('evidencia')->nullable();//pode ser nulo
            $table->mediumText('consequencias')->nullable();//pode ser nulo
            $table->enum('reincidencia', ['Sim', 'Não'])->default('Não');
            $table->string('codVerificacaoAnterior')->nullable();
            $table->integer('numeroGrupoReincidente')->nullable();
            $table->integer('numeroItemReincidente')->nullable();
            $table->enum('itemQuantificado', ['Sim', 'Não'])->default('Não');
            $table->decimal('valorFalta', 8, 2)->default(0);
            $table->decimal('valorSobra', 8, 2)->default(0);
            $table->decimal('valorRisco', 8, 2)->default(0);
            $table->text('orientacao')->nullable();
            $table->mediumText('norma')->nullable();
            $table->enum('situacao', ['Em Inspeção', 'Inspecionado','Corroborado', 'Conferido', 'Pendente na Unidade', 'Pendente na Àrea'])->default('Em Inspeção');
            $table->Text('eventosSistema')->nullable();
            $table->timestamps();
        });


        Schema::table('itensdeinspecoes', function (Blueprint $table)
        {
            $table->index('inspecao_id');
            $table->index('unidade_id');
            $table->index('tipoUnidade_id');
            $table->index('grupoVerificacao_id');
            $table->index('testeVerificacao_id');

            $table->foreign('inspecao_id')
            ->references('id')
            ->on('inspecoes')
            ->onDelete('cascade');

            $table->foreign('unidade_id')
            ->references('id')
            ->on('unidades');

            $table->foreign('tipoUnidade_id')
            ->references('id')
            ->on('tiposdeunidade');


            $table->foreign('grupoVerificacao_id')
            ->references('id')
            ->on('gruposDeVerificacao');

            $table->foreign('testeVerificacao_id')
            ->references('id')
            ->on('testesDeVerificacao');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('itensdeinspecoes');
    }
}

