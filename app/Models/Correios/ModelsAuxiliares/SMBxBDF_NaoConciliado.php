<?php

namespace App\Models\Correios\ModelsAuxiliares;

use Illuminate\Database\Eloquent\Model;

class SMBxBDF_NaoConciliado extends Model
{
    protected $table = 'smb_bdf_naoconciliados';

    protected $fillable=
    [
          'mcu',
          'Agencia',
          'CNPJ',
          'Data',
          'SMBDinheiro',
          'SMBCheque',
          'SMBBoleto',
          'SMBEstorno',
          'BDFDinheiro',
          'BDFCheque',
          'BDFBoleto',
          'Divergencia',
          'Status',
    ];
    protected $casts =
    [
        'Data' => 'date:Y-m-d',
    ];
}
