<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBDFFAT02Table extends Migration
{
   /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bdf_fat_02', function (Blueprint $table) {
            $table->id();
            $table->string('dr')->nullable();//pode ser nulo
            $table->bigInteger('cd_orgao')->nullable();
            $table->string('orgao')->nullable();//pode ser nulo
            $table->bigInteger('ag_postagem')->nullable();
            $table->date('dt_postagem')->nullable();//pode ser nulo
            $table->string('etiqueta')->nullable();//pode ser nulo
            $table->string('servico')->nullable();//pode ser nulo
            $table->decimal('vlr_medida', 8, 2)->default(0);
            $table->string('cd_grupo_pais_destino')->nullable();//pode ser nulo
            $table->string('cep_destino')->nullable();//pode ser nulo
            $table->decimal('vlr_cobrado_destinatario', 8, 2)->default(0);

            $table->string('cod_adm')->nullable();//pode ser nulo
            $table->string('produto')->nullable();//pode ser nulo
            $table->bigInteger('qtde_prestada')->nullable();


            $table->decimal('vlr_declarado', 8, 2)->default(0);
            $table->decimal('vlr_servico', 8, 2)->default(0);
            $table->decimal('vlr_desconto', 8, 2)->default(0);
            $table->decimal('acrescimo', 8, 2)->default(0);
            $table->decimal('vlr_final', 8, 2)->default(0);
            $table->decimal('valor', 8, 2)->default(0);
            $table->bigInteger('cartao')->nullable();
            $table->string('documento')->nullable();//pode ser nulo
            $table->string('servico_adicional')->nullable();//pode ser nulo
            $table->string('nome_servico')->nullable();//pode ser nulo
            $table->string('contrato')->nullable();//pode ser nulo
            $table->string('atendimento')->nullable();//pode ser nulo
            $table->date('dt_mov')->nullable();//pode ser nulo
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
        Schema::dropIfExists('bdf_fat_02');
    }
}
