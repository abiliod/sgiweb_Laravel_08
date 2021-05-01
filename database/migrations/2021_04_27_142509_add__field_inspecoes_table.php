<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldInspecoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inspecoes', function (Blueprint $table) {
            $table->decimal('totalPontos', 6, 2)->default(0)->after('xml')->nullable();
            $table->decimal('valor_ref_itens_inspecionados', 6, 2)->default(0)->after('totalPontos')->nullable();
            $table->decimal('totalpontosnaoconforme', 4, 2)->default(0)->after('valor_ref_itens_inspecionados')->nullable();
            $table->decimal('tnc', 4, 2)->after('totalpontosnaoconforme')->nullable()->default(0);
            $table->integer('totalitensavaliados')->after('tnc')->nullable()->nullable();
            $table->integer('totalitensnaoconforme')->after('totalitensavaliados')->nullable()->nullable();
            $table->string('classificacao')->nullable()->after('totalitensnaoconforme')->nullable();
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

            $table->dropColumn('totalPontos');
            $table->dropColumn('tnc');
            $table->dropColumn('classificacao');
            $table->dropColumn('totalitensavaliados');
            $table->dropColumn('totalitensnaoconforme');
            $table->dropColumn('valor_ref_itens_inspecionados');
            $table->dropColumn('totalpontosnaoconforme');
            $table->dropColumn('tnc');
            $table->dropColumn('classificacao');
            $table->dropColumn('totalitensavaliados');
            $table->dropColumn('totalitensnaoconforme');
        });
    }
}
