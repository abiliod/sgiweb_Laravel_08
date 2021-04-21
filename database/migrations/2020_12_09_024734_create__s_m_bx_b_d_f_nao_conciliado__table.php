<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSMBxBDFNaoConciliadoTable extends Migration
{
   /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('smb_bdf_NaoConciliados', function (Blueprint $table) {
            $table->id();
            $table->string('mcu')->nullable();
            $table->string('Agencia')->nullable();
            $table->string('CNPJ')->nullable();
            $table->date('Data')->nullable();
            $table->decimal('SMBDinheiro', 8, 2)->default(0);
            $table->decimal('SMBCheque', 8, 2)->default(0);
            $table->decimal('SMBBoleto', 8, 2)->default(0);
            $table->decimal('SMBEstorno', 8, 2)->default(0);
            $table->decimal('BDFDinheiro', 8, 2)->default(0);
            $table->decimal('BDFCheque', 8, 2)->default(0);
            $table->decimal('BDFBoleto', 8, 2)->default(0);
            $table->decimal('Divergencia', 8, 2)->default(0);
            $table->string('Status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('smb_bdf_NaoConciliados');
    }
}
