<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePLPListaPendenteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plpListaPendentes', function (Blueprint $table) {
            $table->id();
            $table->string('dr')->nullable();//pode ser nulo
            $table->string('stomcu')->nullable();//pode ser nulo
            $table->string('nome_agencia')->nullable();//pode ser nulo
            $table->string('lista')->nullable();//pode ser nulo
            $table->string('plp')->nullable();//pode ser nulo
            $table->string('objeto')->nullable();//pode ser nulo
            $table->string('cliente')->nullable();//pode ser nulo
            $table->date('dh_lista_postagem')->nullable();//pode ser nulo
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
        Schema::dropIfExists('plpListaPendentes');
    }
}
