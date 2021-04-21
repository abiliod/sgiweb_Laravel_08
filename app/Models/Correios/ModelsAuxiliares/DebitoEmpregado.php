<?php

namespace App\Models\Correios\ModelsAuxiliares;

use Illuminate\Database\Eloquent\Model;

class DebitoEmpregado extends Model
{
    protected $table = 'debitoempregados';
    protected $fillable=
    [
        'cia',
        'conta',
        'data',
        'competencia',
        'lote',
        'tp',
        'sto',
        'nome_unidade',
        'historico',
        'valor',
        'observacoes',
        'documento',
        'matricula',
        'nomeEmpregado',
        'justificativa',
        'regularizacao',
        'acao',
        'regularizacao',
        'anexo',
    ];

    protected $casts =
    [
        'data' => 'date:Y-m-d',

    ];
}
