<?php

namespace App\Models\Correios\ModelsAuxiliares;

use Illuminate\Database\Eloquent\Model;

class FeriasPorMcu extends Model
{
    protected $table = 'ferias_por_mcu';
    protected $fillable=
    [
          'matricula',
          'nome',
          'lotacao',
          'funcao',
          'inicio_fruicao',
          'termino_fruicao',
           'dias',
          
    ];

    protected $casts =
    [
        'inicio_fruicao' => 'date:Y-m-d',
        'inicio_fruicao' => 'date:Y-m-d',
    ];
}
