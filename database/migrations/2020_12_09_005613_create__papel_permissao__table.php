<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePapelPermissaoTable extends Migration
{
/**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('papel_permissao', function (Blueprint $table) {
            $table->bigInteger('permissao_id')->unsigned();
            $table->bigInteger('papel_id')->unsigned();
            $table->foreign('permissao_id')->references('id')->on('permissaos')->onDelete('cascade');
            $table->foreign('papel_id')->references('id')->on('papels')->onDelete('cascade');
            $table->primary(['permissao_id','papel_id']);
       });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('papel_permissao');
    }
}
