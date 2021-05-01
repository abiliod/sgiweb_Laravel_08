<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsItensdeinspecoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('itensdeinspecoes', function (Blueprint $table) {
            $table->decimal('pontuadoInspetor', 6, 2)->default(0)->after('pontuado')->nullable();
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
            $table->dropColumn('pontuadoInspetor');
        });
    }
}
