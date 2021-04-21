<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNovoscamposTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('se')->nullable()->after('activeUser');
            $table->string('seDescricao')->nullable()->after('se');
            $table->string('tipoOrgaoCod')->nullable()->after('seDescricao');
            $table->string('descricao')->nullable()->after('tipoOrgaoCod');
            $table->string('tipoUnidade_id')->nullable()->after('descricao');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('se');
            $table->dropColumn('seDescricao');
            $table->dropColumn('tipoOrgaoCod');
            $table->dropColumn('descricao');
            $table->dropColumn('tipoUnidade_id');
            $table->dropColumn('document');
        });
    }
}
