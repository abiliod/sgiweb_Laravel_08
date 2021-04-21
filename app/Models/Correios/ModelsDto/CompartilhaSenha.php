<?php

namespace App\Models\Correios\ModelsDto;

use Illuminate\Database\Eloquent\Model;

class CompartilhaSenha extends Model
{
    protected $table = "compartilhaSenhas";

    protected $fillable=
        [
            'codigo',
            'grupoVerificacao',
            'numeroDoTeste',
            'matricula',
            'data',
            'evento',
            'tipoafastamento',
        ];
    protected $casts =
        [
            'data' => 'date:Y-m-d',
        ];

}
