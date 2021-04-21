<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInspecoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inspecoes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->biginteger('unidade_id')->unsigned();
            $table->string('descricao')->nullable();
            $table->year('ciclo');
            $table->biginteger('tipoUnidade_id');
            $table->string('tipoVerificacao');
            $table->enum('status', ['Em Inspeção', 'Inspecionado','Em Análise', 'Em Manifestação', 'Concluida'])->default('Em Inspeção');
            $table->string('inspetorcoordenador');
            $table->string('inspetorcolaborador');
            $table->date('datainiPreInspeção');
            $table->string('NumHrsPreInsp')->nullable();
            $table->string('NumHrsDesloc')->nullable();
            $table->string('NumHrsInsp')->nullable();
            $table->text('eventoInspecao')->nullable();
            $table->string('xml')->nullable();
            $table->timestamps();
        });

        Schema::table('inspecoes', function (Blueprint $table)
        {
            $table->index('unidade_id');
            $table->foreign('unidade_id')
                ->references('id')
                ->on('unidades')
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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('inspecoes');

    }


}
