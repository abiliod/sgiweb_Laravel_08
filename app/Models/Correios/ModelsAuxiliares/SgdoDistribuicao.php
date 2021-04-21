<?php

namespace App\Models\Correios\ModelsAuxiliares;

use Illuminate\Database\Eloquent\Model;

class SgdoDistribuicao extends Model {

    protected $table = 'sgdo_distribuicao';

    protected $fillable=
    [
        'dr',
        'unidade',
        'mcu',
        'centralizadora',
        'mcu_centralizadora',
        'distrito',
        'area',
        'locomocao',
        'funcionario',
        'matricula',
        'data_incio_atividade',
        'hora_incio_atividade',
        'data_saida',
        'hora_saida',
        'data_retorno',
        'hora_retorno',
        'data_tpc',
        'hora_do_tpc',
        'data_termino_atividade',
        'hora_termino_atividade',
        'justificado',
        'peso_da_bolsa_kg',
        'peso_do_da_kg',
        'peso_do_da_kg',
        'quantidade_de_da',
        'quantidade_de_gu',
        'quantidade_de_objetos_qualificados',
        'quantidade_de_objetos_coletados',
        'quantidade_de_pontos_de_entregacoleta',
        'quilometragem_percorrida',
        'residuo_simples',
        'residuo_qualificado',
        'almoca_na_unidade',
        'compartilhado',
        'tipo_de_distrito',
    ];

    protected $casts =
    [
        'data_incio_atividade' => 'date:Y-m-d',
        'data_saida' => 'date:Y-m-d',
        'data_retorno' => 'date:Y-m-d',
        'data_tpc' => 'date:Y-m-d',
        'data_termino_atividade' => 'date:Y-m-d',
        'hora_incio_atividade' => 'time:H:m:s',
        'hora_saida' => 'time:H:m:s',
        'hora_retorno' => 'time:H:m:s',
        'hora_do_tpc' => 'time:H:m:s',
        'hora_termino_atividade' => 'time:H:m:s',
    ];
}
