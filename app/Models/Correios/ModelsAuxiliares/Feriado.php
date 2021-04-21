<?php

namespace App\Models\Correios\ModelsAuxiliares;

use Illuminate\Database\Eloquent\Model;

class Feriado extends Model
{

    protected $table = 'feriados';

    protected $fillable=
    [

          'uf',
          'nome_municipio',
          'tipo_feriado',
          'descricao_feriado',
          'data_do_feriado',

    ];

    protected $casts =
    [
        'data_do_feriado' => 'date: m-d',
    ];
}
