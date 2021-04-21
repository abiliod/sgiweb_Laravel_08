<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('coordenacao')->nullable()->after('activeUser');
            $table->string('funcao')->nullable()->after('coordenacao');
            $table->string('localizacao')->nullable()->after('funcao');
            $table->string('telefone_ect')->nullable()->after('localizacao');
            $table->string('telefone_pessoal')->nullable()->after('telefone_ect');
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
            $table->dropColumn('coordenacao');
            $table->dropColumn('funcao');
            $table->dropColumn('localizacao');
            $table->dropColumn('telefone_ect');
            $table->dropColumn('telefone_pessoal');
        });
    }
}
