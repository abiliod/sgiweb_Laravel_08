<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDebitoEmpregadoTable extends Migration
{
        /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('debitoempregados', function (Blueprint $table) {
            $table->id();
            $table->string('cia');
            $table->string('conta');
            $table->string('competencia');
            $table->date('data'); // ->nullable();
            $table->string('lote')->nullable();
            $table->string('tp')->nullable();
            $table->string('sto')->nullable();
            $table->string('nome_unidade');
            $table->mediumText('historico')->nullable();
            $table->decimal('valor', 8, 2)->default(0);
            $table->mediumText('observacoes')->nullable();
            $table->string('documento')->nullable();
            $table->string('matricula')->nullable();
            $table->string('nomeEmpregado')->nullable();
            $table->string('acao')->nullable()->nullable();
            $table->mediumText('justificativa')->nullable();
            $table->string('regularizacao')->nullable();
            $table->string('anexo')->nullable();



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
        Schema::dropIfExists('debitoempregados');
    }
}
