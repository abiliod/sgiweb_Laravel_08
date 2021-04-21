<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldCftvsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cftvs', function (Blueprint $table) {
            $table->string('mcu')->nullable()->after('unidade');
            $table->renameColumn('novo_ip', 'end_ip');
            $table->renameColumn('novo_link', 'link');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropColumn('mcu');
        $table->renameColumn('end_ip', 'novo_ip');
        $table->renameColumn('link', 'novo_link');
    }
}
