<?php

namespace App\Models\Correios\ModelsDto;

use Illuminate\Database\Eloquent\Model;

class PgtoAdicionaisTemp extends Model
{

    protected $table = 'pgto_adicionais_temp';
    protected $fillable=
    [
        'sto',
        'mcu',
        'matricula',
        'cargo',
        'rubrica',
        'ref',
        'valor',
        'situacao',

    ];

}
