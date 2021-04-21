<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldItensdeinspecoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('itensdeinspecoes', function (Blueprint $table) {
            $table->decimal('pontuado', 4, 2)->default(0)->after('eventosSistema');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('itensdeinspecoes', function (Blueprint $table) {
            $table->dropColumn('pontuado');
        });
    }
}
