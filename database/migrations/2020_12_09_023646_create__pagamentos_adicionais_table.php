<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagamentosAdicionaisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagamentos_adicionais', function (Blueprint $table) {
            $table->id();
            $table->string('se')->nullable();//pode ser nulo
            $table->string('sigla_lotacao')->nullable();//pode ser nulo
            $table->string('matricula')->nullable();//pode ser nulo
            $table->string('nome')->nullable();//pode ser nulo
            $table->string('cargo')->nullable();//pode ser nulo
            $table->string('espec')->nullable();//pode ser nulo
            $table->string('titular_da_funcao')->nullable();//pode ser nulo
            $table->string('dif_mer')->nullable();//pode ser nulo
            $table->string('rubrica')->nullable();//pode ser nulo
            $table->bigInteger('qtd')->nullable();
            $table->decimal('valor', 8, 2)->default(0);
            $table->bigInteger('ref')->nullable();//pode ser nulo
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
        Schema::dropIfExists('pagamentos_adicionais');
    }
}
