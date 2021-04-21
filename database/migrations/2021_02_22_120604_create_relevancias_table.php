<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelevanciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relevancias', function (Blueprint $table) {
            $table->id();
            $table->decimal('valor_inicio', 8, 2)->default(0);
            $table->decimal('valor_final', 8, 2)->default(0);
            $table->decimal('fator_multiplicador', 2, 1)->default(0);
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
        Schema::dropIfExists('relevancias');
    }
}
