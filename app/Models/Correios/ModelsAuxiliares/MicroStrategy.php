<?php

namespace App\Models\Correios\ModelsAuxiliares;

use Illuminate\Database\Eloquent\Model;

class MicroStrategy extends Model
{

    protected $table = 'micro_strategys';
    protected $fillable=
    [
          'dr_de_destino',
          'nome_da_unidade',
          'codigo_do_objeto',
          'descricao_do_evento',
          'codigo_do_evento',
          'data_do_evento',

    ];

    protected $casts =
    [
        'data_do_evento' => 'date:Y-m-d',

    ];
}
