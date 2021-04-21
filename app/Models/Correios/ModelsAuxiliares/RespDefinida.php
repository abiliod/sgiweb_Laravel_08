<?php

namespace App\Models\Correios\ModelsAuxiliares;

use Illuminate\Database\Eloquent\Model;

class RespDefinida extends Model
{
    protected $table = 'resp_definidas';

    protected $fillable=
    [
        'unidade',
        'data_pagamento',
        'objeto',
        'datapostagem',
        'servico_produto',
        'valor_da_indenizacao',
        'sto',
        'mcu',
        'subordinacao',
        'nu_pedidoinformacao',
        'se_pagadora',
        'data',
        'nu_sei',
        'nu_sei_abertounidade',
        'situacao',
        'empregadoresponsavel',
        'observacoes',
        'conclusao',
        'providenciaadotada',

    ];

    protected $casts =
    [
        'data_pagamento' => 'date:Y-m-d',
        'datapostagem' => 'date:Y-m-d',
        'data' => 'date:Y-m-d',
    ];
}
