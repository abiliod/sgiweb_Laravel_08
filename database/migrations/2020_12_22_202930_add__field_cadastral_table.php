<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldCadastralTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('cadastral', function (Blueprint $table) {
            $table->string('se')->nullable()->after('data_admissao');
            $table->string('mcu')->nullable()->after('se');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cadastral', function (Blueprint $table) {
            $table->dropColumn('se');
            $table->dropColumn('mcu');
        });
    }
}
