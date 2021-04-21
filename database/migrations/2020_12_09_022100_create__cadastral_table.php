<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCadastralTable extends Migration
{
   /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cadastral', function (Blueprint $table) {
            $table->id();
            $table->string('lotacao')->nullable();
            $table->string('matricula')->nullable();
            $table->string('nome_do_empregado')->nullable();
            $table->string('cargo')->nullable();
            $table->string('especializ')->nullable();
            $table->string('funcao')->nullable();
            $table->date('data_nascto')->nullable();
            $table->string('sexo')->nullable();
            $table->string('situacao')->nullable();
            $table->string('data_admissao')->nullable();


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
        Schema::dropIfExists('cadastral');
    }
}
