<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePgtoAdicionaisTemp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pgto_adicionais_temp', function (Blueprint $table) {
            $table->id();
            $table->string('sto')->nullable();//pode ser nulo
            $table->string('mcu')->nullable();//pode ser nulo
            $table->string('codigo')->nullable();//pode ser nulo codigo numeroGrupoVerificacao numeroDoTeste
            $table->string('numeroGrupoVerificacao')->nullable();//pode ser nulo
            $table->string('numeroDoTeste')->nullable();//pode ser nulo
            $table->string('matricula')->nullable();//pode ser nulo
            $table->string('cargo')->nullable();//pode ser nulo
            $table->string('rubrica')->nullable();//pode ser nulo
            $table->string('ref')->nullable();//pode ser nulo
            $table->string('quantidade')->nullable();//pode ser nulo
            $table->decimal('valor', 8, 2)->default(0);
            $table->string('situacao')->nullable();//pode ser nulo
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
        Schema::dropIfExists('pgto_adicionais_temp');
    }
}
