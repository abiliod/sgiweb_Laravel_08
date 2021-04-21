<?php

namespace App\Models\Correios\ModelsAuxiliares;

use Illuminate\Database\Eloquent\Model;

class PainelExtravio extends Model
{

    protected $table = 'painel_extravios';

    protected $fillable=
    [
        'objeto',
        'data_evento',
        'evento',
        'trecho',
        'evento_trecho',
        'unid_origem',
        'unid_destino',
        'dr_origem',
        'dr_destino',
        'gestao_prealerta',
        'automatico',
        'manual',
        'total',
        'macroprocesso',
        'postado',
        'ultimo_evento_extraviado',
        'ultimo_evento_em_transito',
        'ultimo_evento',
        'ultimo_evento_data',
        'evento_finalizador',
        'tipo',
        'analise_sro',
        'unid_origem_apelido',
        'unid_destino_apelido',
        'trecho_real',
        'se_postagem',
        'unidade_postagem',
        'data_postagem',
        'familia',
        'ultimo_evento_sinistro',

    ];

    protected $casts =
    [
        'data_evento' => 'date:Y-m-d',
        'ultimo_evento_data' => 'date:Y-m-d',
        'data_postagem' => 'date:Y-m-d',
    ];
}
