<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFieldstatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inspecoes', function (Blueprint $table) {
            DB::statement("ALTER TABLE `inspecoes` CHANGE `status` `status` ENUM('Em Inspeção','Inspecionado','Em Análise','Em Manifestação','Concluida','Corroborado') default 'Em Inspeção';");


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inspecoes', function (Blueprint $table) {
            //
        });
    }
}
