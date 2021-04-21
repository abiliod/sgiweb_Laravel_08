<?php

namespace App\Models\Correios\ModelsAuxiliares;

use Illuminate\Database\Eloquent\Model;

class PagamentosAdicionais extends Model
{

    protected $table = 'pagamentos_adicionais';

    protected $fillable=
    [
        'se',
        'sigla_lotacao',
        'matricula',
        'nome',
        'cargo',
        'espec',
        'titular_da_funcao' ,
        'dif_mer',
        'rubrica',
        'qtd',
        'valor',
        'ref'
    ];

}
