<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePapelUsersTable extends Migration
{
        /**
    * Run the migrations.
    *
    * @return void
    */
    public function up() {
        Schema::create('papel_user', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('papel_id')->unsigned();
        });
        Schema::table('papel_user', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('papel_id')->references('id')->on('papels')->onDelete('cascade');
            $table->primary(['user_id','papel_id']);
        });
    }
    /**
    * Reverse the migrations.
    *
    * @return void
    */
    public function down() {
        Schema::dropIfExists('papel_user');
    }
}
