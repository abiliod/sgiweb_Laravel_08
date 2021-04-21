<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampoUsersTable extends Migration
{
  /**
     *
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('document')->nullable()->after('email');
            $table->string('businessUnit')->nullable()->after('document');
            $table->boolean('activeUser')->default(1)->after('businessUnit');

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
            $table->dropColumn('document');
            $table->dropColumn('businessUnit');
            $table->dropColumn('activeUser');
        });
    }
}
