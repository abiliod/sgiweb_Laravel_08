<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRespDefinidasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resp_definidas', function (Blueprint $table) {
            $table->id();
            $table->string('unidade')->nullable();
            $table->date('data_pagamento')->nullable();
            $table->string('objeto')->nullable();
            $table->date('datapostagem')->nullable();
            $table->string('servico_produto')->nullable();
            $table->decimal('valor_da_indenizacao', 8, 2)->default(0);
            $table->string('sto')->nullable();
            $table->string('mcu')->nullable();
            $table->string('subordinacao')->nullable();
            $table->string('nu_pedidoinformacao')->nullable();
            $table->string('se_pagadora')->nullable();
            $table->date('data')->nullable();
            $table->mediumText('nu_sei')->nullable();
            $table->mediumText('nu_sei_abertounidade')->nullable();
            $table->Text('situacao')->nullable();
            $table->Text('empregadoresponsavel')->nullable();
            $table->Text('observacoes')->nullable();
            $table->Text('conclusao')->nullable();
            $table->Text('providenciaadotada')->nullable();
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
        Schema::dropIfExists('resp_definidas');
    }
}
