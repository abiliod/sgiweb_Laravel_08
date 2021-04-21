<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsenteismosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('absenteismos', function (Blueprint $table) {
            $table->id();
            $table->string('matricula')->nullable();//pode ser nulo
            $table->string('nome')->nullable();//pode ser nulo
            $table->string('cargo')->nullable();//pode ser nulo
            $table->string('lotacao')->nullable();//pode ser nulo
            $table->string('motivo')->nullable();//pode ser nulo
            $table->string('dias')->nullable();//pode ser nulo
            $table->date('data_evento')->nullable();


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
        Schema::dropIfExists('absenteismos');
    }
}

