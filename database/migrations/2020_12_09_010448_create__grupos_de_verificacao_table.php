<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGruposDeVerificacaoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gruposdeverificacao', function (Blueprint $table) {
            $table->id();
            $table->Year('ciclo');
            $table->string('tipoVerificacao');
            $table->biginteger('tipoUnidade_id')->unsigned();
            $table->string('numeroGrupoVerificacao')->nullable();
            $table->string('nomegrupo')->nullable();
            $table->timestamps();
        });

        Schema::table('gruposdeverificacao', function (Blueprint $table)
        {
            $table-> unique (['ciclo','tipoVerificacao','tipoUnidade_id', 'numeroGrupoVerificacao'],
            'grupo_de_verificacao_index_unique');
    //  $table->unique(['ciclo','tipoVerificacao','tipoUnidade_id', 'numeroGrupoVerificacao']);
            $table->foreign('tipoUnidade_id')
                ->references('id')
                ->on('tiposdeunidade')
                ->onDelete('cascade');
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
        Schema::dropIfExists('gruposDeVerificacao');
    }
}
