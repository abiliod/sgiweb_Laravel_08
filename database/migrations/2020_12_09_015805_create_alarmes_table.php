<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlarmesTable extends Migration
{
        /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alarmes', function (Blueprint $table) {


            /*opções da tabela*/
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
            $table->autocommit = 0;
            $table->CHECKSUM = 1;
            /*criando colunas*/
            $table->id();
            $table->string('cliente')->nullable();
            $table->string('mcu')->nullable();
            $table->string('usuario')->nullable();
            $table->string('matricula')->nullable();
            $table->string('armedesarme')->nullable();
            $table->date('data')->nullable();
            $table->time('hora')->nullable();
            $table->integer('diaSemana')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alarmes');
    }
}
