<?php

namespace App\Models\Correios\ModelsAuxiliares;

use Illuminate\Database\Eloquent\Model;

class ApontamentoCV extends Model {

    protected $fillable= [
        'dr_detentora',
        'unidade_detentora',
        'origem_destino',
        'tipo_linha',
        'numero_da_linha',
        'controle_viagem' ,
        'numero_ficha_tec',
        'inicio_viagem',
        'sentido',
        'status',
        'sequencia_do_cv',
        'ponto_parada',
        'data_chegada',
        'descricao_ponto_parada',
        'drac_ponto_de_parada',
        'hora_chegada',
        'data_chegada_prevista',
        'horario_chegada_previsto',
        'hodometro_chegada',
        'hodometro_previsto_do_trecho',
        'hodometro_partida',
        'data_partida',
        'hora_partida',
        'data_partida_prevista',
        'horario_partida_previsto',
        'data_conclusao',
        'codigo_ocorrencia' ,
        'descricao_ocorrencia',
        'codigo_transportador',
        'descricao_transportador',
        'no_seq_contrato',
        'item_do_contrato',
        'placa_veiculo',
        'placa_carreta',
    ];
    protected $casts = [

        'inicio_viagem' => 'date:Y-m-d',
        'data_chegada' => 'date:Y-m-d',
        'hora_chegada' => 'time:H:i:s',
        'data_chegada_prevista' => 'date:Y-m-d',
        'horario_chegada_previsto' => 'time:H:i:s',
        'data_partida' => 'date:Y-m-d',
        'hora_partida' => 'time:H:i:s',
        'data_partida_prevista' =>'date:Y-m-d',
        'data_conclusao' => 'date:Y-m-d',
        'horario_partida_previsto' => 'time:H:i:s',

    ];
}
