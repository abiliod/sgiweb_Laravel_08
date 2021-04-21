<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompartilhaSenhaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compartilhaSenhas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->string('numeroGrupoVerificacao');
            $table->string('numeroDoTeste');
            $table->string('matricula');
            $table->date('data')->nullable()->nullable();
            $table->string('evento')->nullable();//pode ser nulo
            $table->string('tipoafastamento')->nullable();//pode ser nulo
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
        Schema::dropIfExists('compartilhaSenhas');
    }
}
