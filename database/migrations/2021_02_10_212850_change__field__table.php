<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeFieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tiposdeunidade', function (Blueprint $table)
        {
            DB::statement("ALTER TABLE `tiposdeunidade` CHANGE `tipoInspecao` `tipoInspecao` ENUM('Ambos','Monitorada','Presencial','Remoto','Suspenso') default NULL;");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tiposdeunidade', function (Blueprint $table)
        {
            DB::statement("ALTER TABLE `tiposdeunidade` CHANGE `tipoInspecao` `tipoInspecao` ENUM('Ambos','Presencial','Remoto','Suspenso') default NULL;");
        });
    }
}
