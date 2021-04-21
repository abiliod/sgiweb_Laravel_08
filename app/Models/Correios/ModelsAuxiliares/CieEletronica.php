<?php

namespace App\Models\Correios\ModelsAuxiliares;

use Illuminate\Database\Eloquent\Model;

class CieEletronica extends Model
{
    protected $table = 'cie_eletronicas';

    protected $fillable=
    [
        'numero',
        'emissao',
        'origem',
        'se_origem',
        'destino',
        'se_destino',
        'irregularidade',
        'categoria',
        'numero_objeto',
        'lida',
        'respondida',
        'fora_do_prazo',
        'data_de_resposta',
        'resposta',

    ];

    protected $casts =
    [
        'emissao' => 'dateTime : Y-m-d H:m:s:u',
        'data_de_resposta' => 'date : Y-m-d',

    ];
}
