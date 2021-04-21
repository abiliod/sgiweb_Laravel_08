<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSL02BdfTable extends Migration
{
    public function up()
    {
        Schema::create('sl02bdfs', function (Blueprint $table) {
            $table->id();
            $table->string('dr')->nullable();
            $table->string('cod_orgao')->nullable();
            $table->string('reop')->nullable();
            $table->string('orgao')->nullable();
            $table->date('dt_movimento')->nullable();
            $table->decimal('saldo_atual', 10, 2)->default(0);
            $table->decimal('limite', 10, 2)->default(0);
            $table->decimal('diferenca', 10, 2)->default(0);
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
        Schema::dropIfExists('sl02bdfs');
    }
}
