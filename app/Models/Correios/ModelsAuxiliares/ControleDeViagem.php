<?php

namespace App\Models\Correios\ModelsAuxiliares;

use Illuminate\Database\Eloquent\Model;

class ControleDeViagem extends Model
{


    protected $table = 'controle_de_viagens';

    protected $fillable=
    [
          'dr_detentora',
          'unidade_detentora',
          'origem_destino',
          'tipo_linha',
          'dr_detentora',
          'numero_da_linha',
          'controle_viagem',
          'numero_ficha_tec',
          'sentido',
          'status',
          'sequencia_do_cv',
          'ponto_parada',
          'descricao_ponto_parada',
          'drac_ponto_de_parada',
          'tipo_de_operacao',
          'quantidade',
          'peso',
          'unitizador',
          'tipo_de_servico',
          'descricao_do_servico',
          'codigo_de_destino',
          'local_de_destino',
          'inicio_viagem',
          'data_chegada_prevista',
          'data_partida_prevista',
          'horario_chegada_prevista',
          'horario_partida_prevista',
    ];

    protected $casts =
    [
        'inicio_viagem' => 'date:Y-m-d',
        'data_chegada_prevista' => 'date:Y-m-d',
        'data_partida_prevista' => 'date:Y-m-d',
        'horario_chegada_prevista' => 'time:H:m:s',
        'horario_partida_prevista' => 'time:H:m:s',
    ];
}
