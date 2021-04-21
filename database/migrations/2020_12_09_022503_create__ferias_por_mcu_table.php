<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeriasPorMcuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ferias_por_mcu', function (Blueprint $table) {
            $table->id();
            $table->string('matricula')->nullable();//pode ser nulo
            $table->string('nome')->nullable();//pode ser nulo
            $table->string('lotacao')->nullable();//pode ser nulo
            $table->string('funcao')->nullable();//pode ser nulo
            $table->date('inicio_fruicao')->nullable();
            $table->date('termino_fruicao')->nullable();
            $table->integer('dias')->default(0);

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
        Schema::dropIfExists('ferias_por_mcu');
    }
}
