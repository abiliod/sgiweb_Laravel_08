<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlidesTable extends Migration
{
      /**
     * Run the migrations.
     * <!-- 26022020 inclusao da funcionalidade Slide-->
     * @return void
     */
    public function up() {
        Schema::create('slides', function (Blueprint $table) {
            $table->id();
            $table->string('titulo')->nullable();
            $table->string('descricao')->nullable();
            $table->string('imagem');
            $table->string('link')->nullable();
            $table->integer('ordem')->nullable();
            $table->enum('publicado',['sim','nao'])->default('nao');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::drop('slides');
    }
}

