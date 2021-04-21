<?php

namespace App\Models\Correios\ModelsDto;

use Illuminate\Database\Eloquent\Model;

class LancamentosSRO extends Model
{
    protected $table = "lancamentossro";

    protected $fillable=
    [
        'codigo',
        'grupoVerificacao',
        'numeroDoTeste',
        'objeto',
        'data',
        'enderecoPostagem',
        'localBaixa1tentativa',
        'falhaDetectada',
        'estado',
    ];
    protected $casts =
    [
        'data' => 'date:Y-m-d',
    ];

}
