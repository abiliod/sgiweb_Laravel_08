<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCieEletronicaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cie_eletronicas', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->nullable();//pode ser nulo
            $table->dateTime('emissao')->nullable();//pode ser nulo
            $table->string('origem')->nullable();//pode ser nulo
            $table->string('se_origem')->nullable();//pode ser nulo
            $table->string('destino')->nullable();//pode ser nulo
            $table->string('se_destino')->nullable();//pode ser nulo
            $table->string('irregularidade')->nullable();//pode ser nulo
            $table->string('categoria')->nullable();//pode ser nulo
            $table->string('numero_objeto')->nullable();//pode ser nulo
            $table->string('lida')->nullable();//pode ser nulo
            $table->string('respondida')->nullable();//pode ser nulo
            $table->string('fora_do_prazo')->nullable();//pode ser nulo
            $table->date('data_de_resposta')->nullable();//pode ser nulo
            $table->string('resposta')->nullable();//pode ser nulo

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
        Schema::dropIfExists('cie_eletronicas');
    }
}
