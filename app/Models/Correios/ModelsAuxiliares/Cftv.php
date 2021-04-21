<?php

namespace App\Models\Correios\ModelsAuxiliares;

use Illuminate\Database\Eloquent\Model;

class Cftv extends Model
{

    protected $table = 'cftvs';

    protected $fillable=
    [
          'unidade',
          'mcu',
          'cameras_fixa_cf',
          'cameras_infra_vermelho_cir',
          'dome',
          'modulo_dvr',
          'no_break',
          'hack',
          'pc_auxiliar',
          'portaweb',
          'end_ip',
          'link',
          'user',
          'password',
          'port',
          'marcamodelo',
          'statusconexao',
          'data_ultima_conexao',
          'observacao',
          'data_no_equipamento',
          'hora_no_equipamento',

    ];

    protected $casts =
    [
        'data_no_equipamento' => 'date:Y-m-d',
        'data_ultima_conexao' => 'date:Y-m-d',
        'hora_no_equipamento' => 'time:H:i:s',

    ];


}
