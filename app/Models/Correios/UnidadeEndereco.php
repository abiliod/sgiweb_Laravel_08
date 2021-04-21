<?php

namespace App\Models\Correios;

use Illuminate\Database\Eloquent\Model;

class UnidadeEndereco extends Model
{

    protected $table = "unidade_enderecos";

    protected $fillable = [
        'codIbge'
        ,'cidade_id'
        ,'endereco'
        ,'numero'
        ,'complemento'
        ,'bairro'
        ,'cidade'
        ,'uf'
        ,'cep'
        ,'mapa'
        ,'latitude'
        ,'longitude'
        ,'mcu'

    ];

}
