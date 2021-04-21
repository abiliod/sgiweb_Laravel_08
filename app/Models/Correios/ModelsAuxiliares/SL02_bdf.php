<?php

namespace App\Models\Correios\ModelsAuxiliares;

use Illuminate\Database\Eloquent\Model;

class SL02_bdf extends Model
{

    protected $table = 'sl02bdfs';

    protected $fillable=
    [
        'dr',
        'cod_orgao',
        'reop',
        'orgao',
        'dt_movimento',
        'saldo_atual',
        'limite',
        'diferenca',
    ];

    protected $casts =
    [
        'dt_movimento' => 'date:Y-m-d',
    ];

}
