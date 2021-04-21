<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaginasTable extends Migration
{
        /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('paginas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('descricao');
            $table->text('texto');
            $table->string('imagem')->nullable();//pode ser nulo
            $table->MEDIUMTEXT('mapa')->nullable();//pode ser nulo
            $table->string('email')->nullable();//pode ser nulo
            $table->string('tipo');
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
        Schema::dropIfExists('paginas');
    }
}
