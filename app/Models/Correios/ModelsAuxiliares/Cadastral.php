<?php

namespace App\Models\Correios\ModelsAuxiliares;

use Illuminate\Database\Eloquent\Model;

class Cadastral extends Model
{

    protected $table = "cadastral";

    protected $fillable=
    [
        'lotacao'
        , 'matricula'
        , 'nome_do_empregado'
        , 'cargo'
        , 'especializ'
        , 'funcao'
        , 'data_nascto'
        , 'sexo'
        , 'situacao'
        , 'data_admissao'
        , 'se'
        , 'mcu'
    ];
    protected $casts =
    [
        'data_nascto' => 'date:Y-m-d',
    ];

}
