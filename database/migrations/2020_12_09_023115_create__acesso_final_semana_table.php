<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcessoFinalSemanaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acessos_final_semana', function (Blueprint $table) {
            $table->id();
            $table->string('mcu')->nullable();//pode ser nulo
            $table->string('evAbertura')->nullable();//pode ser nulo
            $table->string('evDataAbertura')->nullable();//pode ser nulo
            $table->string('evHoraAbertura')->nullable();//pode ser nulo
            $table->string('evFechamento')->nullable();//pode ser nulo
            $table->string('evHoraFechamento')->nullable();//pode ser nulo
            $table->string('diaSemana')->nullable();//pode ser nulo
            $table->string('tempoPermanencia')->nullable();//pode ser nulo
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
        Schema::dropIfExists('acessos_final_semana');
    }
}
