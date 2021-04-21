<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCftvTable extends Migration
{
        /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cftvs', function (Blueprint $table) {
            $table->id();
            $table->string('unidade')->nullable();//pode ser nulo
            $table->string('cameras_fixa_cf')->nullable();//pode ser nulo
            $table->string('cameras_infra_vermelho_cir')->nullable();//pode ser nulo
            $table->string('dome')->nullable();//pode ser nulo
            $table->string('modulo_dvr')->nullable();//pode ser nulo
            $table->string('no_break')->nullable();//pode ser nulo
            $table->string('hack')->nullable();//pode ser nulo
            $table->string('pc_auxiliar')->nullable();//pode ser nulo
            $table->string('portaweb')->nullable();//pode ser nulo
            $table->string('novo_ip')->nullable();//pode ser nulo
            $table->string('novo_link')->nullable();//pode ser nulo
            $table->string('user')->nullable();//pode ser nulo
            $table->string('password')->nullable();//pode ser nulo
            $table->string('port')->nullable();//pode ser nulo
            $table->string('marcamodelo')->nullable();//pode ser nulo
            $table->string('statusconexao')->nullable();//pode ser nulo
            $table->date('data_ultima_conexao')->nullable();//pode ser nulo
            $table->string('observacao')->nullable();//pode ser nulo
            $table->date('data_no_equipamento')->nullable();//pode ser nulo
            $table->time('hora_no_equipamento')->nullable();//pode ser nulo
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
        Schema::dropIfExists('cftvs');
    }
}
