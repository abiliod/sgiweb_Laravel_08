<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLancamentosSROTable extends Migration
{
   /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lancamentossro', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->string('numeroGrupoVerificacao');
            $table->string('numeroDoTeste');
            $table->string('objeto');
            $table->date('data')->nullable()->nullable();;
            $table->string('enderecoPostagem')->nullable();//pode ser nulo
            $table->string('localBaixa1tentativa')->nullable();//pode ser nulo
            $table->string('falhaDetectada')->nullable();//pode ser nulo
            $table->string('estado')->nullable();//pode ser nulo
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
        Schema::dropIfExists('lancamentossro');
    }
}
