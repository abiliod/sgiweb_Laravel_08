<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMicroStrategyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('micro_strategys', function (Blueprint $table) {
            $table->id();
            $table->string('dr_de_destino')->nullable();//pode ser nulo
            $table->string('nome_da_unidade')->nullable();//pode ser nulo
            $table->string('codigo_do_objeto')->nullable();//pode ser nulo
            $table->string('descricao_do_evento')->nullable();//pode ser nulo
            $table->string('codigo_do_evento')->nullable();//pode ser nulo
            $table->date('data_do_evento')->nullable();//pode ser nulo
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
        Schema::dropIfExists('micro_strategys');
    }
}
