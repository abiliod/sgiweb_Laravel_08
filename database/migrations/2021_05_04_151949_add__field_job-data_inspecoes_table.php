<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldJobDataInspecoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inspecoes', function (Blueprint $table) {
            $table->date('data_programacao')->nullable()->after('classificacao');//pode ser nulo
            $table->biginteger('job_programado')->nullable()->after('data_programacao');//pode ser nulo;
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
            $table->dropColumn('data_programacao');
            $table->dropColumn('job_programado');
        });
    }
}
