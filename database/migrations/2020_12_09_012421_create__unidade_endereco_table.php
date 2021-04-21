<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnidadeEnderecoTable extends Migration
{
   /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unidade_enderecos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('mcu')->nullable();//pode ser nulo
            $table->bigInteger('codIbge')->nullable();//pode ser nulo
            $table->bigInteger('cidade_id')->nullable();//pode ser nulo
            $table->string('endereco')->nullable();//pode ser nulo
            $table->string('numero')->nullable();//pode ser nulo
            $table->string('complemento')->nullable();//pode ser nulo
            $table->string('bairro')->nullable();//pode ser nulo
            $table->string('cidade')->nullable();//pode ser nulo
            $table->string('uf')->nullable();//pode ser nulo
            $table->string('cep')->nullable();
            $table->MEDIUMTEXT('mapa')->nullable();//pode ser nulo
            $table->string('latitude')->nullable();//pode ser nulo
            $table->string('longitude')->nullable();//pode ser nulo
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
        Schema::dropIfExists('unidade_enderecos');
    }
}
