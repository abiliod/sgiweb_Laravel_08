<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTiposDeUnidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tiposdeunidade', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->string('sigla');
            $table->string('tipodescricao');
            $table->enum('inspecionar', ['Sim', 'Não'])->default('Não');
            $table->enum('tipoInspecao', ['Ambos', 'Presencial', 'Remoto','Suspenso'])->default('Suspenso');
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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('tiposdeunidade');
    }
}
