<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFieldInspetoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inspecoes', function (Blueprint $table) {

            $table->string('inspetorcoordenador', 15)->nullable()->change();
            $table->string('inspetorcolaborador', 15)->nullable()->change();
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
